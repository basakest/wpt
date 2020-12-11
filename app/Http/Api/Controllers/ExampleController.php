<?php


namespace App\Http\Api\Controllers;

use App\Contracts\Example\IExampleService;
use App\Exceptions\ValidateException;
use App\Http\Api\Dto\ExampleDto\ExampleGetListDto;
use App\Http\Controllers\Controller;
use App\Library\Context;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * 示例服务
     * @var IExampleService $exampleService
     */
    private $exampleService;

    /**
     * ExampleController constructor.
     * @param IExampleService $exampleService
     */
    public function __construct(IExampleService $exampleService)
    {
        $this->exampleService = $exampleService;
    }


    /**
     * 授权成功测试接口
     * @param Request $request
     * @return array
     */
    public function testAuth(Request $request)
    {
        $userInfo = $request->attributes->get("userInfo");
        return [
            "isAuth"    => true,
            "userInfo"  => $userInfo,
            "profile"   => Context::getAttachment("profile"),
            "host"      => $request->getHost(),
        ];
    }

    /**
     * 查询列表示例
     * @param Request $request
     * @return array
     * @throws ValidateException
     */
    public function getExampleList(Request $request)
    {
        $dto = ExampleGetListDto::createFromRequest($request);
        $data = $this->exampleService->getExampleList([
            'id', 'type', 'create_time', 'update_time',
        ], $dto->getFilter(), ["id" => "desc"], $dto->getSkip(), $dto->getPageSize());
        return [
            "list" => $data["list"],
            "page" => $dto->page,
            "total" => $data["total"],
        ];
    }

}