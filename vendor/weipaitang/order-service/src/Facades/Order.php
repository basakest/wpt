<?php


namespace WptOrder\OrderService\Facades;


use Illuminate\Support\Facades\Facade;
use phpDocumentor\Reflection\Types\Object_;
use WptOrder\OrderService\Contracts\OrderApi;

/**
 * Class Order
 * @method static OrderApi adapter(string $name = null)
 * @method static object getOrderById(int $id, array $fields = [])
 * @method static object getOrderByUri(string $uri, array $fields = [])
 * @method static object getOrderAfterFieldsMapped($idOrUri, array $fields, $adapter = null);
 * @method static object getOrderAndSaleById($idOrUri, array $orderFields, array $saleFields, bool $isSnapshot = false, string $adapter = null)
 * @method static object getOrderWithSaleById($idOrUri, array $orderFields, array $saleFields, $isSnapshot = false, string $adapter = null)
 * @method static object getOrderEmptyGetSaleById($idOrUri, array $orderFields, string $adapter = null)
 * @method static Object getOrderList(array $condition, array $fields, array $saleFields = [], int $limit = null, int $offset = null, string $order = '', string $index = '', string $adapter = null)
 * @method static Object getOrderListAttachSale(array $condition, array $fields, array $saleFields = [], int $limit = null, int $offset = null, string $order = '', string $index = '', string $adapter = null)
 * @method static Object getOrderByPid(int $pid, array $condition, array $fields, array $saleFields, string $adapter = null)
 * @method static array getOrderAndSaleListById(array $saleIds, array $fields = [], array $saleFields = [], array $cond = ["isDel" => 0])
 * @method static array getOrderAndSaleListByUris(array $saleUris, array $orderFields, array $saleFields = [], array $orderFilter = [])
 * @method static array getUserSaleStatusSaleListAttchOrderInfo($userInfoId, $saleFields = [], $orderFields = [])
 * @method static array getSaleStatusSaleListAttchOrderInfo($saleIds, $saleFields = [], $orderFields = [])
 * @method static array getSaleIdByPid(int $pid)
 *
 * @package WptOrder\OrderService\Facades\
 */
class Order extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'order';
    }
}