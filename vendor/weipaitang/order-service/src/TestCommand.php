<?php

namespace WptOrder\OrderService;


#use App\Client\Sale\Sale;
use App\ConstDir\SaleConst;
use App\Facades\Sale\Sale;
use App\Utils\CommonUtil;
use Hamcrest\Core\IsEqual;
use Illuminate\Console\Command;
use WptOrder\OrderService\Facades\Order;
use WptOrder\OrderService\OrderService;
use WptOrder\OrderService\Tools\DiffArray;


class TestCommand extends Command
{
    // 启动命令
    protected $signature = 'orderService:test';
    //描述
    protected $description = '订单服务测试命令';

    const ALL_COLUMNS = [
        'id', 'type', 'userinfoId', 'goodsId', 'category', 'secCategory', 'handicraft', 'priceJson',
        'enableReturn', 'expressFee', 'multiWins', 'openTime', 'endTime', 'createTime', 'isDel', 'isShow',
        'profileJson', 'uri', 'status', 'dispute', 'disputeTime', 'isRated', 'unsoldReason',
        'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime',
        'launchTime', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'
    ];

    public function handle()
    {

    }


}