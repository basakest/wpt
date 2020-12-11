<?php


namespace Tests;

use App\Models\OrderModel;
use PHPUnit\Framework\TestCase;
use WptBus\Bus;
use WptBus\Lib\DataCompare;


class DataCompareTest extends TestCase
{
    public function test_ab_a()
    {

        $ab = DataCompare::getInstance()->ab('ab_test_a', function () {
            return 'a';
        }, function () {
            return 'b';
        });
        $this->assertEquals('a', $ab);
        $ab = DataCompare::getInstance()->ab('order_delivery_ab', function () {
            return 'a';
        }, function () {
            return 'b';
        });
        $this->assertEquals('a', $ab);
    }

    public function test_ab_b()
    {
        $ab = DataCompare::getInstance()->ab('ab_test_b', function () {
            return 'a';
        }, function () {
            return 'b';
        });
        $this->assertEquals('b', $ab);
    }

    public function test_check()
    {

        $saleId = 1807274710;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        $deliver = $order->deliver;

        $eq = DataCompare::getInstance()->handle('gray_order_delivery', $deliver, function () {
            $saleId = 1807274710;
            $orderModel = new OrderModel();
            $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
            $deliver = $order->deliver;
            // unset($deliver->__chunk);
            // unset($deliver->addressPostalCode);
            // $deliver->userName = '1';
            return $deliver;
        });
        $this->assertTrue($eq);
    }


    public function test_check2()
    {
        $saleId = 1807274710;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        $deliver = $order->deliver;

        $eq = DataCompare::getInstance()->handle('gray_order_delivery', $deliver, function () {
            $saleId = 1807274710;
            $orderModel = new OrderModel();
            $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
            $deliver = $order->deliver;
            unset($deliver->__chunk);
            return $deliver;
        });
        $this->assertFalse($eq);
    }

    public function test_check3()
    {
        $saleId = 1807274710;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        $deliver = $order->deliver;

        $eq = DataCompare::getInstance()->handle('gray_order_delivery', $deliver, function () {
            $saleId = 1807274710;
            $orderModel = new OrderModel();
            $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
            $deliver = $order->deliver;
            unset($deliver->__chunk);
            return $deliver;
        });
        $this->assertFalse($eq);
    }

    public function test_check4()
    {
        $saleId = 1807274710;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        $deliver = (array)$order->deliver;

        $eq = DataCompare::getInstance()->handle('gray_order_delivery', $deliver, function () {
            $saleId = 1807274710;
            $orderModel = new OrderModel();
            $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
            $deliver = $order->deliver;
            return (array)$deliver;
        });
        $this->assertTrue($eq);
    }

    public function test_check5()
    {
        $saleId = 1807274710;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        $deliver = [$order->deliver, $order->deliver];

        $eq = DataCompare::getInstance()->handle('gray_order_delivery', $deliver, function () {
            $saleId = 1807274710;
            $orderModel = new OrderModel();
            $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
            $deliver = $order->deliver;
            unset($deliver->__chunk);
            return [$deliver, $deliver];
        });
        $this->assertFalse($eq);
    }
}