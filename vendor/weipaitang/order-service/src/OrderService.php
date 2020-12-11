<?php


namespace WptOrder\OrderService;

use App\Facades\Sale\Sale;
use WptCommon\Library\Tools\Logger;
use WptOrder\OrderService\Consts\OrderStatus;
use WptOrder\OrderService\Contracts\Configurable;
use WptOrder\OrderService\Contracts\OrderApi;
use WptOrder\OrderService\Contracts\OrderQuery;
use WptOrder\OrderService\Contracts\OrderUpdate;
use WptOrder\OrderService\Exceptions\InvalidArgumentException;
use WptOrder\OrderService\Exceptions\RequestFailException;
use WptOrder\OrderService\Tools\Log;
use WptCommon\Library\Facades\MLogger;
use SaleService\Modules\Sale as SaleService;

/**
 * 订单服务，单例调用
 *
 * @method object getOrderById(int $id, array $fields)
 * @method object getOrderByUri(string $id, array $fields)
 * @package WptOrder\OrderService
 */
class OrderService implements Configurable
{
    /**
     * 配置信息
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $adapters = [];

    /**
     * @var OrderService
     */
    protected static $instance;

    /**
     * OrderService constructor.
     * @param array $config
     * @throws \Exception
     */
    private function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    private function __clone()
    {
    }

    /**
     * 获取 OrderService 实例
     * @param array $config
     * @return OrderService
     * @throws \Exception
     */
    public static function getInstance(array $config = [])
    {
        if (!static::$instance) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    /**
     * @param array $saleIds
     * @param array $fields
     * @param array $saleFields
     * @param array $orderFilter
     * @return mixed
     */
    public function getOrderAndSaleListById(array $saleIds, array $fields = [], array $saleFields = [], array $cond = ["isDel" => 0])
    {
        $salelist = SaleService::getSaleList($saleIds, $saleFields, $cond);
        $newList = [];
        if ($salelist) {
            $orderlist = Sale::getOrdersWithFilter($saleIds, $fields);
            foreach ((array)$salelist as $key => &$value) {
                if (isset($orderlist[$value->id])) {
                    $value = (object)array_merge((array)$value, (array)$orderlist[$value->id]);
                    $this->mapOrderFields($value);
                    $newList[] = $value;
                } else {
                    MLogger::warning("order-service-2", "getOrderAndSaleListById", [$value->id, "订单缺失"]);
                    continue;
                }
            }
        }
        return $newList;
    }

    /**
     * 根据uri列表获取订单+拍品信息
     * @param array $saleUris
     * @param array $orderFields
     * @param array $saleFields
     * @param array $orderFilter
     * @return array
     */
    public function getOrderAndSaleListByUris(array $saleUris, array $orderFields, array $saleFields = [], array $orderFilter = [])
    {
        $this->checkOrderSaleFields($orderFields, $saleFields);
        !in_array('saleId', $orderFields) and $orderFields[] = 'saleId';
        $saleList = SaleService::getSaleList($saleUris, ['id'], ["isDel" => 0]);
        if (empty($saleList)) return [];
        $saleIds = array_pluck($saleList, 'id');
        $orderlist = Sale::getOrdersWithFilter($saleIds, $orderFields);
        $orderlist = collect($orderlist)->filter(function ($item) {
            return !is_null($item);
        });
        foreach ($orderFilter as $key => $value) {
            if (is_array($value)) {
                $orderlist = $orderlist->whereIn($key, $value);
            } else {
                $orderlist = $orderlist->where($key, $value);
            }
        }
        $orderlist = $orderlist->toArray();
        $this->_attchSales($orderlist, $saleFields);
        return $orderlist;
    }

    /**
     * 根据条件批量获取订单列表
     * @param array $condition
     * @param array $fields
     * @param int|null $limit
     * @param int|null $offset
     * @param string $order
     * @param string $index
     */
    public function getOrderList(array $condition, array $fields, array $saleFields = [], int $limit = null, int $offset = null, string $order = '', string $index = '', string $adapter = null)
    {
        $this->checkOrderSaleFields($fields, $saleFields);
        $list = $this->_getOrderList($condition, $fields, $saleFields, $limit, $offset, $order, $index, false, $adapter);

        return $list;

    }

    /**
     * 根据条件批量获取订单列表和拍品信息
     * @param array $condition
     * @param array $fields
     * @param int|null $limit
     * @param int|null $offset
     * @param string $order
     * @param string $index
     * @param string|null $adapter
     * @return array
     */
    public function getOrderListAttachSale(array $condition, array $fields, array $saleFields = [], int $limit = null, int $offset = null, string $order = '', string $index = '', string $adapter = null)
    {
        $this->checkOrderSaleFields($fields, $saleFields);
        $list = $this->_getOrderList($condition, $fields, $saleFields, $limit, $offset, $order, $index, true, $adapter);
        return $list;
    }

    /**
     * 通过PID 获取订单信息
     * @param int $pid
     * @param array $condition
     * @param array $fields
     * @return array
     */
    public function getOrderByPid(int $pid, array $condition, array $fields, array $saleFields = [], string $adapter = null)
    {
        return $this->_getOrderByPid($pid, $condition, $fields, $saleFields, $adapter);
    }

    private function _getOrderByPid(int $pid, array $condition, array $fields, array $saleFields, string $adapter = null)
    {
        if ($pid) {
            //通过PID获取sale_id 在通过sele_id 批量获取order
            $saleIds = $this->adapter($adapter)->getSaleIdByPid($pid);
            if (empty($saleIds)) {
                return [];
            }
            $orderlist = Sale::getOrdersWithFilter($saleIds, $fields, $condition);

            return array_values(array_map(function (&$value) use ($saleFields) {
                return $this->_attchSaleInfoBySnapshot($value, $saleFields);
            }, $orderlist));
        } else {
            return [];
        }

    }

    private function _getOrderList(array $condition, array $fields, array $saleFields = [], int $limit = null, int $offset = null, string $order = '', string $index = '', $attchSale = false, string $adapter = null)
    {
        $list = $this->adapter($adapter)->getOrdersByCondition($condition, $fields, $limit, $offset, $order, $index);
        if (empty($list)) return [];

        if ($attchSale) return $this->_attchSales($list, $saleFields);

        return array_map(function (&$value) use ($saleFields) {
            return $this->_attchSaleInfoBySnapshot($value, $saleFields);
        }, $list);
    }

    private function _attchSales(&$orderList, $saleFields)
    {
        $saleIds = array_pluck($orderList, 'saleId');
        $salelist = SaleService::getSaleList($saleIds, $saleFields);
        $newList = [];
        foreach ($orderList as $key => &$value) {
            if (isset($salelist[$value->saleId])) {
                $value = (object)array_merge((array)$value, (array)$salelist[$value->saleId]);
                $this->mapOrderFields($value);
                $newList[] = $value;
            } else {
                MLogger::warning("order-service-2", "_getOrderList", [$value->saleId, "拍品缺失"]);
                continue;
            }
        }
        return $newList;
    }

    private function _attchSaleInfoBySnapshot(&$value, $saleFields)
    {
        $snapshot = $value->snapshot ?? '';
        if (!empty($snapshot) && $snapshot != 'null') {
            $snapshot = json_decode($snapshot);

            foreach ($saleFields as $field) {
                $value->$field = $snapshot->$field ?? null;
            }
        }
        $this->_mapping($value, $snapshot);
        unset($value->snapshot);
        return $value;
    }

    private function _mapping(&$value, $snapshot)
    {
        if (property_exists($value, "profileJson") && !empty($value->profileJson)) {
            $value->profile = json_decode($value->profileJson);

            if (property_exists($snapshot, 'content')) {
                $value->profile->content = $snapshot->content;
            }
        }

        if (property_exists($value, "priceJson") && !empty($value->priceJson)) {
            $value->priceJson = $value->priceJson;
            $value->price = json_encode($value->priceJson);
        }
        $this->mapOrderFields($value);
    }

    /**
     * 根据 id 或者 uri 查询订单信息，兼容老的字段（status，unsoldReason）
     * @param       $idOrUri
     * @param array $fields
     * @param null $adapter
     * @return object|null
     */
    public function getOrderAfterFieldsMapped($idOrUri, array $fields, $adapter = null)
    {
        Logger::getInstance()->info('order-service', '请求 getOrderAfterFieldsMapped', ['arguments' => compact("idOrUri", "fields", "adapter")]);

        $order = $this->getOrderByIdOrUri($idOrUri, $fields, $adapter);

        // 字段兼容
        $this->mapOrderFields($order);

        Logger::getInstance()->info('order-service', 'getOrderAfterFieldsMapped 返回', ['order' => $order]);

        return $order;
    }

    /**
     * 获取订单和拍品信息，场景：同时获取订单和拍品字段，订单不存在会返回订单字段默认值
     * @param      $idOrUri
     * @param      $orderFields
     * @param      $saleFields
     * @param bool $isSnapshot
     * @param string $adapter
     * @return mixed
     */
    public function getOrderAndSaleById($idOrUri, array $orderFields, array $saleFields, bool $isSnapshot = false, string $adapter = null)
    {
        Logger::getInstance()->info('order-service', '请求 getOrderAndSaleById', ['arguments' => compact("idOrUri", "orderFields", "saleFields", "isSnapshot", "adapter")]);

        // 检查订单拍品字段
        $this->checkOrderSaleFields($orderFields, $saleFields);

        // 订单拍品对象
        $orderSale = new \stdClass();

        // 请求订单信息
        $order = $this->getOrderByIdAndFields($idOrUri, $orderFields, $isSnapshot, $adapter);

        // 订单不存在将订单中的公共字段追加到拍品字段
        if (empty($order)) {
            $commonField = $this->getSaleFieldByOrderFieldFromCommon($orderFields);
            $orderFields = array_values(array_diff($orderFields, $commonField));
            $saleFields = array_values(array_unique(array_merge($saleFields, $commonField)));
        }

        // 获取拍品信息，是否从订单信息快照返回，否则从请求拍品信息
        if ($isSnapshot && isset($order->snapshot)) {
            $sale = $this->getSaleInfoFromSnapshot($order->snapshot, $saleFields);
        } else {
            $sale = $this->getSaleInfoFromSaleApi($idOrUri, $saleFields);
        }

        // 存在订单不存在拍品报错
        if ($order && empty($sale)) {
            throw new RequestFailException("sale-api调用失败微服务调用失败. method: " . __METHOD__ . ", arguments: " . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }

        // 订单拍品都不存在返回空
        if (empty($order) && empty($sale)) {
            return null;
        }

        // 订单为空获取默认信息
        if (empty($order)) {
            $order = $this->getOrderDefaultInfo($orderFields, $sale);
        }

        // 组装订单信息
        $this->assembleOrderSaleByOrder($orderSale, $order);

        // 组装拍品信息
        $this->assembleOrderSaleBySale($orderSale, $sale);

        Logger::getInstance()->info('order-service', 'getOrderAndSaleById 返回', ['order' => $orderSale]);

        return $orderSale;
    }

    /**
     * 获取订单附带拍品信息，场景：存在订单情况下附带拍品信息，订单不存在时返回空
     * @param        $idOrUri
     * @param        $orderFields
     * @param        $saleFields
     * @param bool $isSnapshot
     * @param string $adapter
     * @return mixed
     */
    public function getOrderWithSaleById($idOrUri, array $orderFields, array $saleFields, $isSnapshot = false, string $adapter = null)
    {
        Logger::getInstance()->info('order-service', '请求 getOrderWithSaleById', ['arguments' => compact("idOrUri", "orderFields", "saleFields", "isSnapshot", "adapter")]);

        // 检查订单拍品字段
        $this->checkOrderSaleFields($orderFields, $saleFields);

        // 订单拍品对象
        $orderSale = new \stdClass();

        // 请求订单信息
        $order = $this->getOrderByIdAndFields($idOrUri, $orderFields, $isSnapshot, $adapter);
        if (empty($order)) {
            return null;
        }

        // 获取拍品信息，是否从订单信息快照返回，否则从请求拍品信息
        if ($isSnapshot) {
            $sale = $this->getSaleInfoFromSnapshot($order->snapshot, $saleFields);
        } else {
            $sale = $this->getSaleInfoFromSaleApi($idOrUri, $saleFields);
        }

        // 存在订单不存在拍品报错
        if ($order && empty($sale)) {
            throw new RequestFailException("sale-api调用失败微服务调用失败. method: " . __METHOD__ . ", arguments: " . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }

        // 组装订单信息
        $this->assembleOrderSaleByOrder($orderSale, $order);

        // 组装拍品信息
        $this->assembleOrderSaleBySale($orderSale, $sale);

        Logger::getInstance()->info('order-service', 'getOrderWithSaleById 返回', ['order' => $orderSale]);

        return $orderSale;
    }

    /**
     * 获取订单信息，订单空时从获取拍品，场景：查询的都是订单字段，当订单不存在时需要获取拍品获取拍品字段（userinfoId, status, endTime）
     * @param             $idOrUri
     * @param             $orderFields
     * @param string|null $adapter
     * @return mixed|object|null
     */
    public function getOrderEmptyGetSaleById($idOrUri, array $orderFields, string $adapter = null)
    {
        Logger::getInstance()->info('order-service', '请求 getOrderEmptyGetSaleById', ['arguments' => compact("idOrUri", "orderFields", "adapter")]);

        // 检查订单字段
        $this->checkOrderFields($orderFields);

        // 订单拍品对象
        $orderSale = new \stdClass();

        // 请求订单信息
        $order = $this->getOrderByIdOrUri($idOrUri, $orderFields, $adapter);

        // 订单为空从拍品获取
        if (empty($order)) {

            // 构建拍品字段
            $commonField = $this->getSaleFieldByOrderFieldFromCommon($orderFields);
            $orderFields = array_values(array_diff($orderFields, $commonField));
            $saleFields = $commonField;

            // 请求拍品信息
            $sale = $this->getSaleInfoFromSaleApi($idOrUri, $saleFields);
            if (empty($sale)) {
                return null;
            }

            // 订单获取默认对象
            $order = $this->getOrderDefaultInfo($orderFields, $sale);
        }

        // 组装订单信息
        $this->assembleOrderSaleByOrder($orderSale, $order);

        // 组装拍品信息
        if (isset($sale)) {
            $this->assembleOrderSaleBySale($orderSale, $sale);
        }

        Logger::getInstance()->info('order-service', 'getOrderEmptyGetSaleById 返回', ['order' => $orderSale]);

        return $orderSale;
    }


    /**
     * 获取用户拍品列表并添加订单信息
     * @param int $userInfoId
     * @param array $saleStatus
     * @param array $saleFields
     * @param array $orderFields
     * @return array
     */
    public function getUserSaleStatusSaleListAttchOrderInfo($userInfoId, $saleFields = [], $orderFields = [])
    {
        $saleList = SaleService::getUserSaleList($userInfoId, 0, ['sale'], $saleFields);
        if (empty($saleList)) return [];
        $this->_saleListAttchOrderInfo($saleList, $orderFields);
        return $saleList;
    }

    /**
     * 根据saleId获取拍品列表并添加订单信息
     * @param array $saleIds
     * @param array $saleFields
     * @param array $orderFields
     * @param array $status
     * @return array
     */
    public function getSaleStatusSaleListAttchOrderInfo($saleIds, $saleFields = [], $orderFields = [])
    {
        $saleList = $this->_getSaleListByIdAndStatus($saleIds, ['sale'], $saleFields);
        if (empty($saleList)) return [];
        $this->_saleListAttchOrderInfo($saleList, $orderFields);
        return $saleList;
    }

    /**
     * 拍品服务暂时不支持in条件，所以单独查询之后聚合
     * @param $saleIds
     * @param $status
     * @param $saleFields
     * @return array
     */
    private function _getSaleListByIdAndStatus($saleIds, $status, $saleFields)
    {
        if (!empty($status)) {
            $saleList = [];
            foreach ($status as $item) {
                $tmp = SaleService::getSaleList($saleIds, $saleFields, ['status' => $item, 'isDel' => 0]) ?? [];
                $saleList = array_merge($saleList, $tmp);
            }
        } else {
            $saleList = SaleService::getSaleList($saleIds, $saleFields) ?? [];
        }
        return $saleList;
    }


    /**
     * 检查订单字段
     * @param $orderFields
     */
    private function checkOrderFields($orderFields)
    {
        if (!is_array($orderFields)) {
            throw new InvalidArgumentException("参数错误，订单字段必须为数组");
        }

        $diff = array_diff($orderFields, get_order_field());
        if (!empty($diff)) {
            throw new InvalidArgumentException("参数错误，不存在的订单字段: " . implode(",", $diff));
        }
    }

    /**
     * 检查拍品字段
     * @param $saleFields
     */
    private function checkSaleFields($saleFields)
    {
        if (!is_array($saleFields)) {
            throw new InvalidArgumentException("参数错误，拍品字段必须为数组");
        }
    }

    /**
     * 检查订单拍品字段
     * @param $orderFields
     * @param $saleFields
     */
    private function checkOrderSaleFields($orderFields, $saleFields)
    {
        // 检查订单字段
        $this->checkOrderFields($orderFields);

        // 检查拍品字段
        $this->checkSaleFields($saleFields);
    }

    /**
     * 通过订单字段从公共字段获取拍品字段
     * @param $orderFields
     * @return array
     */
    private function getSaleFieldByOrderFieldFromCommon($orderFields)
    {
        $orderSaleCommonField = get_order_and_sale_common_field();
        $saleFields = array_values(array_intersect($orderSaleCommonField, $orderFields));
        if (in_array('saleId', $orderFields) && !in_array('id', $saleFields)) {
            $saleFields[] = 'id';
        }
        return $saleFields;
    }

    /**
     * 获取订单通过id或uri
     * @param string|int $idOrUri 订单id或者uri
     * @param array $fields 查询字段
     * @param string|null $adapter 指定需要调用的api，目前只支持 saleGo
     * @return object|null
     */
    private function getOrderByIdOrUri($idOrUri, array $fields, string $adapter = null)
    {
        $idType = gettype($idOrUri);
        if (!in_array($idType, ['integer', 'string'])) {
            throw new InvalidArgumentException("参数错误，只能通过订单id或者uri查询订单");
        }

        // id 和 type 字段查询兼容
        foreach ($fields as $index => $field) {
            if ($field == 'id') {
                $fields[$index] = 'saleId';
            }
            if ($field == 'type') {
                $fields[$index] = 'saleType';
            }
        }

        $order = $idType === 'string'
            ? $this->adapter($adapter)->getOrderByUri($idOrUri, $fields)
            : $this->adapter($adapter)->getOrderById($idOrUri, $fields);

        if (empty($order)) return null;

        return $order;
    }

    /**
     * 获取订单通过Id和字段
     * @param $idOrUri
     * @param $orderFields
     * @param $isSnapshot
     * @param string $adapter
     * @return mixed
     */
    private function getOrderByIdAndFields($idOrUri, $orderFields, $isSnapshot, string $adapter = null)
    {
        if ($isSnapshot) {
            $orderFields[] = 'snapshot';
        }

        return $this->getOrderByIdOrUri($idOrUri, $orderFields, $adapter);
    }

    /**
     * 组装订单拍品通过订单
     * @param $order
     * @param $orderSale
     */
    private function assembleOrderSaleByOrder(&$orderSale, $order)
    {
        // 赋值
        foreach ($order as $field => $item) {
            if (!isset($orderSale->$field)) {
                $orderSale->$field = $item;
            }
        }

        // 订单字段转化
        $this->mapOrderFields($orderSale);
    }

    /**
     * 组装订单拍品通过拍品
     * @param $sale
     * @param $orderSale
     */
    private function assembleOrderSaleBySale(&$orderSale, $sale)
    {
        foreach ($sale as $field => $item) {
            if (!isset($orderSale->$field)) {
                $orderSale->$field = $item;
            }
        }
    }

    /**
     * 拍品列表附加订单信息
     * @param $saleList
     * @param $orderFields
     * @return mixed
     */
    private function _saleListAttchOrderInfo(&$saleList, $orderFields)
    {
        $saleIds = array_pluck($saleList, 'id');
        $orders = $this->getOrderList(['saleId' => $saleIds], $orderFields);
        $orders = collect($orders)->keyBy('saleId');
        foreach ($saleList as &$item) {
            $order = new \stdClass();
            if (!isset($orders[$item->id])) {
                $this->setOrderDeaultInfoForSale($orderFields, $item);
            } else {
                foreach ($orders[$item->id] as $key => $value) {
                    $item->$key = $value;
                }
            }
        }
        return $saleList;
    }

    private function setOrderDeaultInfoForSale($orderFields, &$sale)
    {
        $order = $this->getOrderDefaultInfo($orderFields, $sale);
        $this->mapOrderFields($order);
        foreach ($order as $key => $value) {
            $sale->$key = $value;
        }
    }

    /**
     * 获取订单默认信息
     * @param $orderFields
     * @param $sale
     * @return \stdClass
     * @author lht
     */
    private function getOrderDefaultInfo($orderFields, $sale)
    {
        $orderDefault = new \stdClass();
        if (in_array('saleId', $orderFields)) {
            $orderDefault->saleId = $sale->id;
        }
        foreach ($orderFields as $field) {
            if (!isset($sale->$field) && $field != 'saleId') {
                $orderDefault->$field = get_order_field_default_val($field);
            }
        }

        return $orderDefault;
    }

    /**
     * 从快照获取拍品信息
     * @param $snapshotJson
     * @param $saleFields
     * @return \stdClass
     */
    private function getSaleInfoFromSnapshot($snapshotJson, $saleFields)
    {
        // 从快照获取拍品信息
        $saleSnapshot = json_decode($snapshotJson);
        // 字段做转化
        $this->mapSaleFields($saleSnapshot);
        // 组装需要的字段
        $sale = new \stdClass();
        foreach ($saleFields as $field) {
            $sale->$field = $saleSnapshot->$field ?? null;
        }
        return $sale;
    }

    /**
     * 从sale服务获取拍品信息
     * @param $idOrUri
     * @param $saleFields
     * @return mixed
     */
    private function getSaleInfoFromSaleApi($idOrUri, $saleFields)
    {
        //return Sale::getSale($idOrUri, $saleFields);
        return SaleService::getSale($idOrUri, $saleFields);
    }

    /**
     * 兼容老的sale字段
     * @param $sale
     */
    protected function mapSaleFields(&$sale)
    {
        // profileJson priceJson winJson 新sale接口返回，老sale接口不返回
        // content enableIdent 新sale接口支持传字段，老sale接口不支持
        // goodsId和draftId 新sale接口传哪个返回哪个，老sale接口支持goodsId
        // handicraft 暂时没有应用场景

        // 从快照信息需要处理字段
        // 1. profileJson priceJson winJson解开
        // 2. goodsId从draftId拿
        // 3. systemBzjJson快照不支持

        // 快照不支持字段
        // systemBzjJson
        // profileJson里面除img[0]、content、video之外的其它字段

        // draftId => goodsId
        $sale->goodsId = $sale->draftId;

        // profile
        $sale->profile = json_decode($sale->profileJson);
        $sale->profile->content = $sale->content;

        // price
        $sale->price = json_decode($sale->priceJson);

        // status
        $sale->status = get_sale_status((int)$sale->status);
    }

    /**
     * 兼容老的sale字段
     * @param $order
     */
    protected function mapOrderFields(&$order)
    {
        if (isset($order->winJson)) {
            $order->win = json_decode($order->winJson);
        }

        if (isset($order->status)) {
            $order->status = get_order_status_text((int)$order->status);
        }

        if (isset($order->unsoldReason)) {
            $order->unsoldReason = get_unsold_reason_text((int)$order->unsoldReason);
        }

        if (isset($order->saleId)) {
            $order->id = $order->saleId;
        }
        if (isset($order->saleType)) {
            $order->type = $order->saleType;
        }
    }

    /**
     * 获取配置信息
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * 初始化/重置 配置信息
     * @param array $config
     */
    public function setConfig(array $config = [])
    {
        $this->config = $this->mergeConfig($config);
    }

    /**
     * 配置合并
     * @param array $config
     * @return array
     */
    protected function mergeConfig(array $config)
    {
        static $default;
        if (!isset($default)) {
            $default = include __DIR__ . "/../config/order.php";
        }

        return array_merge($default, $config);
    }

    /**
     * 获取api实例
     * @param string $adapterName
     * @return OrderApi
     */
    public function resolveApiAdapter(string $adapterName): OrderApi
    {
        if (!isset($this->adapters[$adapterName]) || !$this->adapters[$adapterName] instanceof OrderApi) {
            $adapter = "\\WptOrder\\OrderService\\Apis\\" . ucfirst($adapterName);
            if (!class_exists($adapter)) {
                throw new InvalidArgumentException("不存在的api, adapter: {$adapterName}");
            }

            $this->adapters[$adapterName] = new $adapter($this);
        }

        return $this->adapters[$adapterName];
    }

    /**
     * @param string|null $name
     * @return OrderApi
     */
    public function adapter(string $name = null): OrderApi
    {
        $name = $name ?: $this->getDefaultApiAdapter();

        if (empty($name)) {
            throw new InvalidArgumentException("没有可用的api适配器");
        }

        return $this->adapters[$name] ?? $this->resolveApiAdapter($name);
    }

    /**
     * @return mixed|string
     */
    protected function getDefaultApiAdapter()
    {
        return $this->getConfig()['api'] ?? '';
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->adapter()->{$method}(...$arguments);
    }

}