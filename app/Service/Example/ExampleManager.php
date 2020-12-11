<?php


namespace App\Service\Example;

use App\Contracts\Example\IExampleService;
use App\Exceptions\ResourceNotFoundException;
use App\Library\QueryHelper;
use App\Service\Example\Model\ExampleModel;
use App\Service\Example\Query\ExampleQuery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExampleManager implements IExampleService
{

    /**
     * @inheritDoc
     * @throws ResourceNotFoundException
     */
    public function getExampleList(array $fields, array $filters, array $orderBys = [], $skip = 0, $limit = 20): array
    {
        try {
            $qb = ExampleModel::query();

            $qb = QueryHelper::select($qb, $fields, ExampleQuery::SELECT_ABLES);
            $qb = QueryHelper::filter($qb, $filters, ExampleQuery::FILTER_ABLES);
            $qb = QueryHelper::orderBy($qb, $orderBys, ExampleQuery::ORDER_ABLES);

            $task = $qb->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException('任务配置不存在');
        }

        return $task->toArray();
    }
}