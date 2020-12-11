<?php


namespace WptBus\Service\Sale\Module;


use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class SaleLike extends BaseService
{
    /**
     * 去围观
     * @param string $saleUri 拍品uri
     * @param int $userinfoId 用户id
     * @param string $nickName 用户昵称
     * @param string $headImgUrl 用户头像
     * @param int $createTime 围观时间
     * @return array
     */
    public function toLike(string $saleUri, int $userinfoId, string $nickName, string $headImgUrl, int $createTime)
    {
        $data = [
            'Uri' => $saleUri,
            'UserInfoId' => $userinfoId,
            'Nickname' => $nickName,
            'HeadImgUrl' => $headImgUrl,
            'CreateTime' => $createTime
        ];

        $result = $this->httpPost(Router::LIKE_TO_LIKE, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    /**
     * 新取消围观接口
     * @param int $saleId
     * @param int $userinfoId
     * @return array
     */
    public function cancelLike(int $saleId, int $userinfoId)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId
        ];

        $result = $this->httpPost(Router::LIKE_CANCEL_LIKE, $data);
        $this->dealResultData($result, function ($data){
            return $data;
        });

        return $result;
    }


	public function isLikeSale ($userinfoId, $saleId){
		$data = [
			"SaleId" => $saleId,
			"UserInfoId" =>$userinfoId,
		];

		$result = $this->httpPost(Router::IS_LIKE_SALE, $data);
		$this->dealResultData($result, function ($data) {
			return $data;
		});
		return $result;
	}

	public function getSaleLikeList ($saleId, $fields = [], $score = '', $limit = 10){
		$data = [
			"SaleId" => $saleId,
			"Columns" =>$fields,
			"Num"=>$limit,
			"Score" => $score
		];

		$result = $this->httpPost(Router::GET_SALE_LIKE_LIST, $data);
		$this->dealResultData($result, $this->formatStringResult());
		return $result;
	}

	/**
	 * @param $userinfoId
	 * @param array $fields
	 * @param string $score
	 * @param int $limit
	 * @return array
	 */
	public function get7DayLikeSaleList ($userinfoId, $fields = [], $score = '', $limit = 10)
	{
		$data = [
			"UserInfoId" =>$userinfoId,
			"Fields"=> $fields,
			"Score"=> $score,
			"Limit"=> $limit
		];

		$result = $this->httpPost(Router::GET_7DAY_LIKE_SALE_LIST, $data);
		$this->dealResultData($result, $this->formatStringResult());
		return $result;
	}

	public function getLikeSaleList ($userinfoId, $fields = [], $score = "", $limit = 10)
	{
		$data = [
			"UserInfoId" =>$userinfoId,
			"Fields"=> $fields,
			"Score"=> $score,
			"Limit"=> $limit
		];

		$result = $this->httpPost(Router::GET_LIKE_SALE_LIST, $data);
		$this->dealResultData($result, $this->formatStringResult());
		return $result;
	}

	// 根据拍品id删除围观信息
	public function deleteLikesBySaleId ($saleId){
		$data = [
			"SaleId" =>$saleId,
		];

		$result = $this->httpPost(Router::DELETE_BY_SALE_ID, $data);
		$this->dealResultData($result, $this->formatStringResult());
		return $result;
	}

	public function get7DaySaleCount ($userinfoId){
		$data = [
			"UserInfoId" =>$userinfoId
		];

		$result = $this->httpPost(Router::GET_7DAY_LIKE_SALE_COUNT, $data);
		$this->dealResultData($result, function ($data) {
			return $data;
		});
		return $result;
	}

	/**
	 * @param $userinfoId
	 * @param int $category
	 * @param array $fields
	 * @param string $score
	 * @param int $limit
	 * @return array
	 */
	public function getLikeSaleListByCategory ($userinfoId, $category = -1, $fields = [], $score = '', $limit = 10)
	{
		$data = [
			"UserInfoId" =>$userinfoId,
			"Fields"=> $fields,
			"Category" => $category,
			"Score"=> $score,
			"Limit"=> $limit
		];

		$result = $this->httpPost(Router::GET_LIKE_SALE_LIST_BY_CATEGORY, $data);
		$this->dealResultData($result, $this->formatStringResult());
		return $result;
	}

	protected function formatStringResult()
	{
		return function ($data) {
			if (!empty($data) && is_string($data)) {
				return json_decode($data, true);
			}
			return [];
		};
	}
}