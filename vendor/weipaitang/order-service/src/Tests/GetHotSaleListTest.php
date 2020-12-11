<?php

namespace WptOrder\OrderService\Tests;

use App\ConstDir\BaseConst;
use App\Facades\Sale\Sale;
use SaleService\Modules\Sale as SaleService;
use WptCommon\Library\Facades\MLogger;
use WptOrder\OrderService\Consts\OrderStatus;
use WptOrder\OrderService\Facades\Order;
use WptOrder\OrderService\Tools\DiffArray;


class GetHotSaleListTest extends TestCase
{
    public function test_toMergeDelivery()
    {
        $uris = ['19103016099mw5c2', '1910301609qc5ood', '1910301609ojt7qu', '1910301609gmh0d2', '1910301609us1mrk', '1910301609edtne4', '1910301609woo5xt', '1910301609ay9vze', '1910301609ntplvv', '1910301609d0e7ym', '191030160825oojv', '191030160890k6u9', '1910301608d3bpuv', '1910301608pnkm6e', '1910301608p85lxy', '1910301608u47qrk', '1910301608mrieqa', '1910301608fjlblv', '1910301608ulibrl', '1910301613ul69iv', '1910301613t4o5ag', '1910301613ppwqqw', '1910301613sl6q3w', '19103016137v7mfj'];
        $fields = ['id', 'uri', 'category', 'secCategory', 'userinfoId', 'profileJson', 'status', 'type', 'paidTime', 'winJson', 'winUserinfoId'];
        $where = [
            'uri' => $uris,
            'status' => ['paid', 'refunding']
        ];
        $saleList = Sale::getHotSaleList($fields, $where);

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($uris) {
            $saleList = Order::getOrderAndSaleListByUris(
                $uris,
                ['saleId', 'userinfoId', 'status', 'saleType', 'paidTime', 'winJson', 'winUserinfoId', 'snapshot'],
                ['id', 'uri', 'category', 'secCategory', 'profileJson'],
                ['status' => [OrderStatus::PAID, OrderStatus::REFUNDING]]);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    public function test_mergeDelivery()
    {
        $saleIds = [1971547911, 1963425256, 1936279307, 1877057114, 1865428041, 1870239964];
        $winUserinfoId = 1;

        $where = [
            'id in (' . implode(',', $saleIds) . ') ' => null,
            'winUserinfoId' => $winUserinfoId
        ];
        $fields = ['id', 'uri', 'userinfoId', 'profileJson', 'expressFee', 'status', 'type', 'paidTime', 'winUserinfoId'];
        $saleList = Sale::getHotSaleList($fields, $where, 'paidTime desc');

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($saleIds, $winUserinfoId) {
            $saleList = Order::getOrderListAttachSale([
                'saleId' => $saleIds,
                "winUserinfoId" => $winUserinfoId
            ], ['saleId', 'userinfoId', 'status', 'saleType', 'paidTime', 'winUserinfoId', 'snapshot'], ['expressFee', 'profileJson'],
                null, null, 'paidTime desc');
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_repayList()
    {
        $saleIds = [1971547911, 1963425256, 1936279307, 1877057114, 1865428041, 1870239964];
        $where = [
            'id in (' . implode(',', $saleIds) . ') ' => null,
        ];
        $fields = ['id', 'uri', 'userinfoId', 'winJson', 'profileJson', 'expressFee', 'status', 'type', 'paidTime', 'winUserinfoId'];
        $saleList = Sale::getHotSaleList($fields, $where);

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($saleIds) {
            $saleList = Order::getOrderListAttachSale([
                'saleId' => $saleIds,
            ], ['saleId', 'userinfoId', 'winJson', 'status', 'saleType', 'paidTime', 'winUserinfoId', 'snapshot'], ['id', 'uri', 'profileJson', 'expressFee',],
                null, null, 'paidTime desc');
            return $saleList;
        }, __METHOD__, ['profile.depotPdPrice']);
        $this->assertEquals(true, $eq);
    }

    public function test_getRelateSalesList()
    {
        $saleUri = '19090521442r1ux3';
        $winUserinfoId = 1;
        $page = 1;
        $pageSize = BaseConst::PAGE_NUM * 2;
        $conditions = [
            'type IN(0, 6, 7, 8, 10, 11)' => null,
            'winUserinfoId' => $winUserinfoId,
            'isDel' => 0,
            "status IN('delivery', 'refunding', 'returning', 'agreeReturn', 'deliveryReturn', 'finished')" => null,
            'deliveryTime >' => time() - 86400 * 30
        ];
        if ($saleUri) {
            $conditions['uri'] = $saleUri;
        }

        $list = Sale::getHotSaleList(
            ['id', 'type', 'category', 'secCategory', 'enableReturn', 'expressFee', 'uri', 'userinfoId', 'profileJson', 'winJson', 'isRated', 'dispute', 'unsoldReason', 'status', 'paidTime', 'disputeTime', 'delayReceiptTime', 'delayPayTime', 'deliveryTime', 'launchTime', 'finishedTime', 'openTime', 'createTime', 'endTime'],
            $conditions,
            ['paidTime' => 'DESC'],
            $pageSize + 1,
            ($page - 1) * $pageSize
        );

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $list, function () use ($conditions, $pageSize, $page) {
            $orderConditions = [
                'saleType' => [0, 6, 7, 8, 10, 11],
                'status' => [3, 5, 7, 8, 10, 4],
                "winUserinfoId" => $conditions['winUserinfoId']
            ];
            if (isset($conditions['deliveryTime >'])) {
                $orderConditions['deliveryTime >'] = $conditions['deliveryTime >'];
            }
            if (isset($conditions['uri'])) {
                $sale = Sale::getSale($conditions['uri'], ['id']);
                $orderConditions['saleId'] = $sale->id ?? 0;
            }

            $list = Order::getOrderListAttachSale($orderConditions,
                ['saleId', 'saleType', 'userinfoId', 'winJson', 'isRated', 'dispute', 'unsoldReason',
                    'status', 'paidTime', 'disputeTime', 'delayReceiptTime', 'delayPayTime',
                    'deliveryTime', 'launchTime', 'finishedTime', 'endTime'],
                ['category', 'secCategory', 'enableReturn', 'expressFee', 'uri', 'openTime', 'createTime', 'profileJson'],
                $pageSize + 1, ($page - 1) * $pageSize, 'paidTime desc');
            return $list;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_getRelateSalesList2()
    {
        $saleUri = '';
        $winUserinfoId = 1;
        $page = 1;
        $pageSize = BaseConst::PAGE_NUM * 2;
        $conditions = [
            'type IN(0, 6, 7, 8, 10, 11)' => null,
            'winUserinfoId' => $winUserinfoId,
            'isDel' => 0,
            "status IN('delivery', 'refunding', 'returning', 'agreeReturn', 'deliveryReturn', 'finished')" => null,
            'deliveryTime >' => time() - 86400 * 30
        ];
        if ($saleUri) {
            $conditions['uri'] = $saleUri;
        }

        $list = Sale::getHotSaleList(
            ['id', 'type', 'category', 'secCategory', 'enableReturn', 'expressFee', 'uri', 'userinfoId', 'profileJson', 'winJson', 'isRated', 'dispute', 'unsoldReason', 'status', 'paidTime', 'disputeTime', 'delayReceiptTime', 'delayPayTime', 'deliveryTime', 'launchTime', 'finishedTime', 'openTime', 'createTime', 'endTime'],
            $conditions,
            ['paidTime' => 'DESC'],
            $pageSize + 1,
            ($page - 1) * $pageSize
        );

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $list, function () use ($conditions, $pageSize, $page) {
            $orderConditions = [
                'saleType' => [0, 6, 7, 8, 10, 11],
                'status' => [OrderStatus::DELIVERY, OrderStatus::REFUNDING, OrderStatus::RETURNING, OrderStatus::AGREE_RETURN, OrderStatus::DELIVERY_RETURN, OrderStatus::FINISHED],
                "winUserinfoId" => $conditions['winUserinfoId']
            ];
            if (isset($conditions['deliveryTime >'])) {
                $orderConditions['deliveryTime >'] = $conditions['deliveryTime >'];
            }
            if (isset($conditions['uri'])) {
                $sale = Sale::getSale($conditions['uri'], ['id']);
                $orderConditions['saleId'] = $sale->id ?? 0;
            }

            $list = Order::getOrderListAttachSale($orderConditions,
                ['saleId', 'saleType', 'userinfoId', 'winJson', 'isRated', 'dispute', 'unsoldReason',
                    'status', 'paidTime', 'disputeTime', 'delayReceiptTime', 'delayPayTime',
                    'deliveryTime', 'launchTime', 'finishedTime', 'endTime'],
                ['category', 'secCategory', 'enableReturn', 'expressFee', 'uri', 'openTime', 'createTime', 'profileJson'],
                $pageSize + 1, ($page - 1) * $pageSize, 'paidTime desc');
            return $list;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_checkMergeDelivery()
    {
        $saleIds = [1952385382, 1936073616, 1924435749, 1865428041, 1852971763];
        $winUserinfoId = 1;
        $fields = ['id', 'uri', 'userinfoId', 'profileJson', 'status', 'type', 'paidTime', 'winJson', 'winUserinfoId', 'dispute'];
        $where = [
            'id in (' . implode(',', $saleIds) . ') ' => null,
            'winUserinfoId' => $winUserinfoId
        ];
        $saleList = Sale::getHotSaleList($fields, $where);

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($saleIds, $winUserinfoId) {
            $saleList = Order::getOrderList([
                'saleId' => $saleIds,
                'winUserinfoId' => $winUserinfoId
            ], ['userinfoId', 'status', 'saleType', 'paidTime', 'winJson', 'winUserinfoId', 'dispute', 'snapshot', 'saleId'], ['uri', 'profileJson']);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    public function test_getUnPaidRecommendSealSale()
    {
        $uid = 1;
        $sales = Sale::getHotSaleList(
            ['uri', 'profileJson', 'id'],
            ['winUserinfoId' => $uid, 'type' => '4', 'status' => 'deal', 'isDel' => 0],
            null,
            1
        );

        // *********订单迁移2019.10.24 二期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $sales, function () use ($uid) {
            $sales = Order::getOrderListAttachSale(
                ['winUserinfoId' => $uid, 'saleType' => '4', 'status' => 1],
                ['saleId'], ['uri', 'profileJson'], 1);
            return $sales;
        }, __METHOD__, ['profile.recommendPromotion']);
        $this->assertEquals(true, $eq);
    }


    public function test_ForbiddenShopCommand()
    {
        $uid = 1;
        $where = [
            'userinfoId' => $uid,
            'isDel' => 0,
            'status' => ['sale', 'deal'],
        ];
        $columns = ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson', 'multiWins', 'endTime', 'profileJson', 'uri', 'status', 'winJson', 'winUserinfoId', 'systemBzjJson'];
        $saleList = Sale::getHotSaleList($columns, $where);

        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($uid) {
            $saleListBySale = Order::getUserSaleStatusSaleListAttchOrderInfo($uid, ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson',
                'multiWins', 'endTime', 'profileJson', 'uri', 'status', 'systemBzjJson'], ['winJson', 'winUserinfoId', 'saleId']);
            $saleListByOrder = Order::getOrderListAttachSale(['userinfoId' => $uid, 'status' => OrderStatus::DEAL],
                ['winJson', 'winUserinfoId', 'status', 'saleId'],
                ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson', 'multiWins', 'endTime', 'profileJson', 'uri', 'systemBzjJson']);
            $saleList = array_merge((array)$saleListBySale, (array)$saleListByOrder);
            return $saleList;
        }, __METHOD__, ['systemBzj.']);

        $this->assertEquals(true, $eq);
    }

    public function test_SaleDelCommand()
    {
        $saleIdArr = [1865432063, 1873716985, 1879979469, 1932604745];
        $where = [
            'id' => $saleIdArr,
            'isDel' => 0,
            'status' => ['sale', 'deal'],
        ];
        $columns = ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson',
            'multiWins', 'endTime', 'profileJson', 'uri', 'status', 'winJson', 'winUserinfoId', 'systemBzjJson'];
        $saleList = Sale::getHotSaleList($columns, $where);

        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($saleIdArr) {
            $saleField = ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson',
                'multiWins', 'endTime', 'profileJson', 'uri', 'status', 'systemBzjJson'];
            $orderFields = ['winJson', 'winUserinfoId', 'saleId'];
            $saleListBySale = Order::getSaleStatusSaleListAttchOrderInfo($saleIdArr, $saleField, $orderFields);
            $saleListByOrder = Order::getOrderListAttachSale(['saleId' => $saleIdArr, 'status' => OrderStatus::DEAL],
                ['winJson', 'winUserinfoId', 'status', 'saleId'],
                ['id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'priceJson', 'multiWins', 'endTime', 'profileJson', 'uri', 'systemBzjJson']);
            $saleList = array_merge((array)$saleListBySale, (array)$saleListByOrder);
            return $saleList;
        }, __METHOD__, ['systemBzj']);

        $this->assertEquals(true, $eq);
    }
}