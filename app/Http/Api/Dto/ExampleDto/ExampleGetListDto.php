<?php


namespace App\Http\Api\Dto\ExampleDto;

use App\Contracts\Common\CampaignConst;
use App\Exceptions\ValidateException;
use App\Http\Api\Dto\Dto;

/**
 * Class ExampleGetListDto
 * @package App\Http\Api\Dto\CampaignDto
 *
 * @property integer $page
 * @property integer $pageNum
 */
class ExampleGetListDto extends Dto
{
    /**
     * @inheritDoc
     */
    public static function getRules()
    {
        return [
            'state'     => 'nullable|integer|in:'.implode(",", CampaignConst::STATES),
            'page'      => 'nullable|integer|min:1',
            'pageNum'   => 'nullable|integer|min:1|max:500',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getErrorMessage()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getAttributes()
    {
        return [
            'state'    => '状态',
            'page'     => '页码',
            'pageNum'  => '每页记录数',
        ];
    }

    public function getSkip()
    {
        return ($this->page - 1) * $this->pageNum;
    }

    public function getPageSize()
    {
        return !empty($this->pageNum) ? $this->pageNum : 1000;
    }

    /**
     * @return array
     * @throws ValidateException
     */
    public function getFilter()
    {
        $filters = [
            "delete_time" => 0
        ];
        $filters = $this->appendNotEmptyData("state", "state", $filters);
        return $filters;
    }
}