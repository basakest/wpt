<?php

namespace WptOrder\OrderService\Tests;

use App\ConstDir\BaseConst;
use App\ConstDir\OrderConst;
use App\ConstDir\SaleConst;
use App\Facades\Sale\Sale;
use App\Facades\Userinfo\Userinfo;
use App\Libraries\message\SendMessage;
use App\Models\OrderModel;
use App\Utils\CommonUtil;
use SaleService\Modules\Sale as SaleService;
use WptCommon\Library\Facades\MLogger;
use WptOrder\OrderService\Consts\OrderStatus;
use WptOrder\OrderService\Consts\SaleStatus;
use WptOrder\OrderService\Facades\Order;
use WptOrder\OrderService\Tools\DiffArray;
use WptOrder\OrderService\Tools\Efficiency;


class GetCountTest extends TestCase
{
    public function test_LiveUtilLogic_checkWaitPay()
    {
        /**
         * select * from pc.sale where status = 'deal' and isDel = 0 and userinfoId = 886959 and winUserinfoId = 10061177 and delayPayTime >=1573699629;
         * select * from sale_order.sale_order where status = 1 and delayPayTime  >= 1573699629 and userinfoId = 886959 and winUserinfoId = 10061177;
         */
        $sellerId = 886959;
        $buyerId = 10061177;
        $nowTime = time();
        $where = [
            'status' => 'deal',
            'isDel' => 0,
            'userinfoId' => $sellerId,
            'winUserinfoId' => $buyerId,
            'delayPayTime >=' => $nowTime,
        ];

        $waitPayCount = Sale::getSaleCount($where, true);

        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount_hot', $waitPayCount, function () use ($buyerId, $sellerId, $nowTime) {
            $where = [
                'status' => OrderStatus::DEAL,
                'userinfoId' => $sellerId,
                'winUserinfoId' => $buyerId,
                'delayPayTime >=' => $nowTime,
            ];
            $waitPayCount = (int)Sale::getOrderCount($where);
            return $waitPayCount;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_getSaleNum()
    {
        /**
         * select * from pc.sale where status in ('sale','deal','paid','delivery','refunding','refundpause','returning','agreeReturn','returnpause','deliveryReturn') and userinfoId = 886959;
         *
         * select * from sale.sale where userinfoid = 886959 and status in (2) and isDel = 0;
         * select * from sale_order.sale_order where status in (1,2,3,5,6,7,8,9,10) and userinfoid = 886959;
         */
        $uid = 886959;
        $status = [
            'sale',
            'deal',
            'paid',
            'delivery',
            'refunding',
            'refundpause',
            'returning',
            'agreeReturn',
            'returnpause',
            'deliveryReturn',
        ];
        $where = ['status' => $status, 'isDel' => 0, 'userinfoId' => $uid];
        $saleCount = Sale::getSaleCount($where);
        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount', $saleCount, function () use ($uid) {
            $saleCount = (int)\SaleService\Modules\Sale::getCount($uid, ['sale'], 0);
            $orderCount = (int)Sale::getOrderCount([
                'status' => [OrderStatus::DEAL, OrderStatus::PAID, OrderStatus::DELIVERY,
                    OrderStatus::REFUNDING, OrderStatus::REFUNDPAUSE, OrderStatus::RETURNING,
                    OrderStatus::AGREE_RETURN, OrderStatus::RETURNPAUSE, OrderStatus::DELIVERY_RETURN],
                'userinfoId' => $uid
            ]);
            $saleCount = $saleCount + $orderCount;
            return $saleCount;
        }, __METHOD__);
        $this->assertEquals(true, $eq);

    }

    public function test_getSaleNumFinishedInService()
    {
        /**
         * select * from pc.sale where status = "finished" and isDel = 0 and enableReturn = 1 and deliveryTime > 1573095396 and userinfoId = 886959;
         *
         * select * from sale_order.sale_order where status in (1,2,3,5,6,7,8,9,10) and userinfoId = 886959;
         */
        $uid = 886959;
        $before7DayTime = time() - 7 * 24 * 3600;
        $where = [
            'status' => 'finished',
            'isDel' => 0,
            'enableReturn' => 1,
            'userinfoId' => $uid,
            'deliveryTime >' => $before7DayTime,   //发货时间在7天内，已完结但在包退期
        ];
        $saleCount = (int)Sale::getSaleCount($where);

        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount', $saleCount, function () use ($uid, $before7DayTime) {
            $saleIds = Order::getOrderList(['status' => OrderStatus::FINISHED, 'userinfoId' => $uid, 'deliveryTime >' => $before7DayTime], ['saleId']);
            if (empty($saleIds)) return 0;
            $saleIds = array_pluck($saleIds, 'saleId');
            $saleIds = \SaleService\Modules\Sale::getSaleList($saleIds, ['id'], ['enableReturn' => 1]);
            $saleCount = count($saleIds);
            return $saleCount;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_getPaidSaleCount()
    {
        /**
         * select count(1) from pc.sale where status in ('paid') and userinfoId = 43606741;
         * select count(1) from sale_order.sale_order where status = 2 and userinfoId = 43606741;
         */
        $uid = 886959;
        $saleCount = Sale::getSaleCount(['status' => 'paid', 'userinfoId' => $uid]);

        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount_hot', $saleCount, function () use ($uid) {
            $saleCount = (int)Sale::getSellerPaidOrderCount($uid);
            return $saleCount;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }


    public function test_getUserInfoForNoUpdateTel1()
    {
        /**
         * select count(1) from sale.sale where userinfoId = ? and status not in ('unsold','finished') and isDel = 0
         * =>
         * select count(1) from new_sale.sale where userinfoId = ? and status in ('notpaybzj', 'sale')
         * select count(1) from new_sale.sale_order where userinfoId = ? and status in (1,2,3,5,6,7,8,9,10)
         */
        $uid = 886959;
        $where = [
            'userinfoId' => $uid,
            'status not in' => sprintf(" ('%s', '%s')", 'unsold', 'finished'),
            'isDel' => 0
        ];
        $sellerSaleNum = Sale::getSaleCount($where);

        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount', $sellerSaleNum, function () use ($uid) {
            $saleCount = (int)\SaleService\Modules\Sale::getCount($uid, ['notpaybzj', 'sale'], 0);
            $orderCount = (int)Sale::getOrderCount(['userinfoId' => $uid, 'status' => [
                OrderStatus::DEAL, OrderStatus::PAID, OrderStatus::DELIVERY,
                OrderStatus::REFUNDING, OrderStatus::REFUNDPAUSE, OrderStatus::RETURNING,
                OrderStatus::AGREE_RETURN, OrderStatus::RETURNPAUSE, OrderStatus::DELIVERY_RETURN
            ]]);
            $sellerSaleNum = $saleCount + $orderCount;
            return $sellerSaleNum;
        }, __METHOD__ . 'seller');

        $this->assertEquals(true, $eq);
    }

    public function test_getUserInfoForNoUpdateTel2()
    {
        /**
         * select count(1) from sale.sale where winUserinfoId = ? and status not in ('unsold','finished') and isDel = 0
         * =>
         * select count(1) from new_sale.sale_order where winUserinfoId = ?  and status in (1,2,3,5,6,7,8,9,10)
         */
        $uid = 10061177;
        $where = [
            'winUserinfoId' => $uid,
            'status not in' => sprintf(" ('%s', '%s')", 'unsold', 'finished'),
            'isDel' => 0
        ];
        $buyerSaleNum = Sale::getSaleCount($where);

        // *********订单迁移三期get-count @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_getcount', $buyerSaleNum, function () use ($uid) {
            $buyerSaleNum = (int)Sale::getOrderCount(['winUserinfoId' => $uid, 'status' => [
                OrderStatus::DEAL, OrderStatus::PAID, OrderStatus::DELIVERY,
                OrderStatus::REFUNDING, OrderStatus::REFUNDPAUSE, OrderStatus::RETURNING,
                OrderStatus::AGREE_RETURN, OrderStatus::RETURNPAUSE, OrderStatus::DELIVERY_RETURN
            ]]);
            return $buyerSaleNum;
        }, __METHOD__ . 'buyer');

        $this->assertEquals(true, $eq);
    }

}