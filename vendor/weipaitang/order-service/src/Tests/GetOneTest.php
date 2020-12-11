<?php


namespace WptOrder\OrderService\Tests;


use App\ConstDir\SaleConst;
use App\Facades\Sale\Sale;
use WptOrder\OrderService\Facades\Order;
use WptOrder\OrderService\Tools\DiffArray;

class GetOneTest extends TestCase
{
    function test_getOen()
    {
        dd('null' == "");
        $info = Sale::getSaleExtend(2002207565, null);
        $this->assertNotEmpty($info);
        $info = Sale::getSale(2002207565, null, ['isDel' => 0]);
        $this->assertNotEmpty($info);
    }

    public function test_ContinueTradingCommand()
    {
        $item['id'] = 2002207565;
        $columns = ['id', 'type', 'uri', 'winUserinfoId', 'priceJson', 'systemBzjJson', 'profileJson', 'winJson'];
        $orderColumns = ['winUserinfoId', 'winJson'];
        $saleColumns = ['id', 'type', 'uri', 'priceJson', 'profileJson', 'systemBzjJson'];
        $sale = Sale::getOne($columns, ['id' => $item['id']]);

        // *********订单迁移二期2019.10.24 *********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($item) {
            $orderColumns = ['winUserinfoId', 'winJson'];
            $saleColumns = ['id', 'type', 'priceJson', 'profileJson', 'systemBzjJson'];
            $sale = Order::getOrderWithSaleById($item['id'], $orderColumns, $saleColumns);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_RateTimeEndCommand()
    {
        $saleId = 2002207565;
        $columns = ['id', 'paidTime', 'userinfoId', 'winUserinfoId', 'isRated', 'uri', 'recommendTime'];
        $sale = Sale::getOne($columns, ['id' => $saleId]);

        // *********订单迁移二期2019.10.29 *********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId) {
            $orderColumns = ['winUserinfoId', 'paidTime', 'isRated', 'userinfoId'];
            $saleColumns = ['id', 'recommendTime', 'uri'];
            $sale = Order::getOrderWithSaleById($saleId, $orderColumns, $saleColumns);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_RateTimeOutNoticeCommand()
    {
        $saleId = 2002207565;
        $sale = Sale::getOne(SaleConst::ALL_COLUMNS, ['id' => $saleId, 'status' => 'finished', 'isDel' => 0, 'isRated' => 0]);

        // *********订单迁移二期2019.10.29 *********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId) {
            $sale = [];
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated',];

            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $_sale = Order::getOrderAndSaleById($saleId, $orderColumns, $saleColumns);
            if (empty($_sale)) return $sale;
            if ($_sale->status == 'finished' || $_sale->isDel == 0 || $_sale->isRated == 0) {
                $sale = $_sale;
            }
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_RefundBzjBuyerCommand()
    {
        $saleId = 2002207565;
        $sale = Sale::getOne(SaleConst::ALL_COLUMNS, ['id' => $saleId]);
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId) {
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated'];
            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderWithSaleById($saleId, $orderColumns, $saleColumns, false);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_RefundBzjSellerCommand()
    {
        $saleId = 2002207565;
        $sale = Sale::getOneById(SaleConst::ALL_COLUMNS, ['id' => $saleId]);

        // *********订单迁移二期2019.10.25*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId) {
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated',];

            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderWithSaleById($saleId, $orderColumns, $saleColumns);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_ThawTimeEndCommand()
    {
        $thawBalance = new \stdClass();
        $thawBalance->saleId = 2002207565;
        $sale = Sale::getOne(SaleConst::ALL_COLUMNS, ['id' => $thawBalance->saleId]);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($thawBalance) {
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated'];
            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderWithSaleById($thawBalance->saleId, $orderColumns, $saleColumns);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_CategoryPromotionHandle_getPromotionState()
    {
        $saleId = 2002207565;
        $userinfoId = 4;

        $columns = ['winUserinfoId', 'profileJson', 'category', 'secCategory'];
        $saleInfo = Sale::getOne($columns, [
            'winUserinfoId' => $userinfoId,
            'pid' => $saleId,
            'isDel' => 0
        ]);
        if (!empty($saleInfo)) {
            $sale = $saleInfo;
            // *********订单迁移二期2019.10.29 *********
            $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId, $userinfoId) {
                $sale = new \stdClass();
                // 根据pid获取 saleId 列表
                $saleIds = Order::getSaleIdByPid($saleId);
                // 根据 saleId 获取订单列表
                if (!empty($saleIds)) {
                    $saleList = Order::getOrderAndSaleListById($saleIds, ['winUserinfoId'], ['profileJson', 'category', 'secCategory']);
                    if (!empty($saleList)) {
                        foreach ($saleList as $item) {
                            if ($item->winUserinfoId == $userinfoId) {
                                $sale = $item;
                            }
                        }
                    }
                }
                return $sale;
            }, __METHOD__);

            $this->assertEquals(true, $eq);
        }
    }

    public function test_CategoryPromotionHandle_promotionReceiveSeal()
    {
        $saleId = 2002207565;
        $columns = ['isDel', 'type', 'status', 'uri', 'id', 'winUserinfoId', 'userinfoId', 'winJson', 'profileJson', 'category', 'secCategory', 'paidTime'];
        $sale = Sale::getOne($columns, ['id' => $saleId]);

        // *********订单迁移二期2019.10.29 *********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($saleId) {
            $orderFields = ['status', 'winUserinfoId', 'winJson', 'paidTime'];
            $saleFields = ['type', 'uri', 'id', 'userinfoId', 'profileJson', 'category', 'secCategory', 'isDel'];
            $sale = Order::getOrderWithSaleById($saleId, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_FriendPayLogic_residue()
    {
        $balanceLog = new \stdClass();
        $balanceLog->saleUri = '1910301504rwvijn';

        $fields = ['id', 'type', 'uri', 'unsoldReason', 'status', 'paidTime', 'profileJson', 'winJson', 'winUserinfoId'];
        $where = ['uri' => $balanceLog->saleUri, 'isDel' => 0];
        $saleInfo = Sale::getOne($fields, $where, '', true);

        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $saleInfo, function () use ($balanceLog) {
            $saleFields = ['id', 'type', 'uri', 'profileJson'];
            $orderFields = ['unsoldReason', 'status', 'paidTime', 'winJson', 'winUserinfoId'];
            $saleInfo = Order::getOrderWithSaleById($balanceLog->saleUri, $orderFields, $saleFields);
            return $saleInfo;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_PayLogic_residue()
    {
        $uri = '1910301504rwvijn';
        $columns = ['id', 'uri', 'goodsId', 'userinfoId', 'status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
        $sale = Sale::getOne($columns, ['uri' => $uri], '', true);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($uri) {
            $saleFields = ['id', 'uri', 'draftId', 'userinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
            $orderFields = ['status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId'];
            $sale = Order::getOrderWithSaleById($uri, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_PayOldLogic_residue()
    {
        $uri = '1910301504rwvijn';
        $columns = ['id', 'uri', 'goodsId', 'userinfoId', 'status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
        $sale = Sale::getOne($columns, ['uri' => $uri], '', true);
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($uri) {
            $saleFields = ['id', 'uri', 'draftId', 'userinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
            $orderFields = ['status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId'];
            $sale = Order::getOrderWithSaleById($uri, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_PayOldV2Logic_residue()
    {
        $uri = '1910301504rwvijn';
        $columns = ['id', 'uri', 'goodsId', 'userinfoId', 'status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
        $sale = Sale::getOne($columns, ['uri' => $uri], '', true);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($uri) {
            $saleFields = ['id', 'uri', 'draftId', 'userinfoId', 'priceJson', 'profileJson', 'type', 'expressFee', 'category', 'secCategory', 'enableReturn', 'isDel', 'pid', 'recommendTime'];
            $orderFields = ['status', 'paidTime', 'delayPayTime', 'winJson', 'winUserinfoId'];
            $sale = Order::getOrderWithSaleById($uri, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_SaleManageLogic_toDownNotice()
    {
        $uri = '1910301504rwvijn';
        $sale = Sale::getOne(SaleConst::ALL_COLUMNS, ['uri' => $uri]);

        // *********订单迁移二期2019.10.25*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($uri) {
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated',];

            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];

            return Order::getOrderWithSaleById($uri, $orderColumns, $saleColumns);
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    public function test_SaleFactory_resolveSale()
    {
        $orderResidue = new \stdClass();
        $orderResidue->saleId = 2002206354;

        $sale = Sale::getOne(SaleConst::ALL_COLUMNS, ['id' => $orderResidue->saleId], '', true);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_getone', $sale, function () use ($orderResidue) {
            $orderColumns = ['unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
                'launchTime', 'status', 'dispute', 'disputeTime', 'isRated'];
            $saleColumns = ['id', 'type', 'userinfoId', 'draftId', 'category', 'secCategory', 'priceJson',
                'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
                'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderWithSaleById($orderResidue->saleId, $orderColumns, $saleColumns);
            return $sale;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

}