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
use WptOrder\OrderService\Facades\Order;
use WptOrder\OrderService\Tools\DiffArray;
use WptOrder\OrderService\Tools\Efficiency;


class GetAllSaleListTest extends TestCase
{

    public function setUp()
    {
        ini_set('memory_limit', '1G');
        parent::setUp();
    }

    public function test_UpdateDisputingSellerListCommand()
    {
        $where = [
            'isDel' => 0,
            'status' => ['paid', 'delivery', 'refunding', 'refundpause', 'returning', 'agreeReturn', 'returnpause', 'deliveryReturn'],
            'dispute' => 2,
            'disputeTime > 1551050590' => null,
        ];
        $saleList = Sale::getAllSaleList(['userinfoId', 'id'], $where);

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage_preview', $saleList, function () {
            $condition = [
                'status' => [OrderStatus::PAID, OrderStatus::DELIVERY, OrderStatus::REFUNDING, OrderStatus::REFUNDPAUSE, OrderStatus::RETURNING, OrderStatus::AGREE_RETURN, OrderStatus::RETURNPAUSE, OrderStatus::DELIVERY_RETURN],
                'dispute' => 2,
                'disputeTime > 1551050590' => null,
            ];
            $saleList = Order::getOrderList($condition, ['userinfoId', 'saleId'], [], null, null, '', 'idx_status_delayReceiptTime_dispute_saleId');
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_sortFieldContent1()
    {
        $newData = '[{"isDel":0,"content":"191112047商品","openTime":1573561369,"pid":0,"recommendTime":0,"isShow":1,"draftId":917146328,"id":2010525605,"secCategory":3,"likes":7,"type":0,"expressFee":"freePost","uri":"1911122022krsojo","price":{"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":10,"endTime":"2019-11-12 20:24:49","fixedPrice":0,"increase":38,"referencePrice":0},"createTime":1573561369,"isShare":0,"multiWins":1,"views":0,"profile":{"cert":"","category":0,"isFirstEnd":1,"secCategory":3,"isLiveOrder":1,"liveShow":1,"liveOrderCharge":2,"tagId":"","userinfoId":47137615,"imgs":["20191112304g25v3-ii29-2y54-rvy2-573561366320-W1080H1920"],"isLiveSale":1,"title":"","content":"191112047商品"},"category":0,"userinfoId":47137615,"systemBzj":["48319713","48356020"],"enableReturn":1,"endTime":1573561499,"goodsId":917146328,"profileJson":"{\"cert\":\"\",\"category\":0,\"isFirstEnd\":1,\"secCategory\":3,\"isLiveOrder\":1,\"liveShow\":1,\"liveOrderCharge\":2,\"tagId\":\"\",\"userinfoId\":47137615,\"imgs\":[\"20191112304g25v3-ii29-2y54-rvy2-573561366320-W1080H1920\"],\"isLiveSale\":1,\"title\":\"\",\"content\":\"191112047\\u5546\\u54c1\"}","priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":10,\"endTime\":\"2019-11-12 20:24:49\",\"fixedPrice\":0,\"increase\":38,\"referencePrice\":0}","systemBzjJson":"[\"48319713\",\"48356020\"]"}]';
        $oldData = '[{"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":10,\"endTime\":\"2019-11-12 20:24:49\",\"fixedPrice\":0,\"increase\":38,\"referencePrice\":0}","expressFee":"freePost","views":0,"price":{"fixedPrice":0,"increase":38,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":10,"endTime":"2019-11-12 20:24:49"},"userinfoId":47137615,"category":0,"pid":0,"type":0,"secCategory":3,"createTime":1573561369,"likes":0,"profile":{"secCategory":3,"tagId":"","liveShow":1,"cert":"","category":0,"liveOrderCharge":2,"userinfoId":47137615,"isLiveOrder":1,"isFirstEnd":1,"imgs":["20191112304g25v3-ii29-2y54-rvy2-573561366320-W1080H1920"],"content":"191112047商品","title":"","isLiveSale":1},"goodsId":917146328,"multiWins":1,"endTime":1573561499,"isDel":0,"profileJson":"{\"cert\":\"\",\"content\":\"191112047商品\",\"secCategory\":3,\"title\":\"\",\"category\":0,\"isFirstEnd\":1,\"isLiveSale\":1,\"userinfoId\":47137615,\"liveShow\":1,\"tagId\":\"\",\"imgs\":[\"20191112304g25v3-ii29-2y54-rvy2-573561366320-W1080H1920\"],\"isLiveOrder\":1,\"liveOrderCharge\":2}","recommendTime":0,"uri":"1911122022krsojo","isShow":1,"id":2010525605,"systemBzj":[48356020,48319713],"openTime":1573561369,"isShare":0,"systemBzjJson":"[48356020,48319713]","enableReturn":1}]';
        $newData = json_decode($newData);
        $oldData = json_decode($oldData);
        $eq = DiffArray::transfer('', $oldData, function () use ($newData) {
            return $newData;
        });
        $this->assertEquals(true, $eq);


    }

    public function test_sortFieldContent2()
    {
        $newData = '{"systemBzj":["187552","853508","4667417","5027541","21703632","45031031","46806713"],"category":1,"type":0,"recommendTime":0,"price":{"delayTime":10,"endTime":"2019-11-12 20:21:57","fixedPrice":0,"increase":2,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0},"uri":"1911122018glqfzt","createTime":1573561137,"delayPayTime":0,"views":15,"endTime":1573561317,"status":"sale","content":"东方之珠保11-16","id":2010523009,"profile":{"imgs":["201911121yv0z713-8fgf-76c3-q08j-573561122245-W720H1280"],"category":1,"title":"","cert":"","enableIdent":1,"liveShow":1,"userinfoId":40277963,"fee":{"goodshopServices":{"feeName":"优店商家 - 技术服务费","feeType":"goodshopServices","fee":4}},"secCategory":1010,"tagId":"","isFirstEnd":1,"isLiveSale":1,"content":"东方之珠保11-16"},"openTime":1573561137,"isDel":0,"likes":0,"secCategory":1010,"userinfoId":40277963,"multiWins":1,"expressFee":"freePost","profileJson":"{\"imgs\":[\"201911121yv0z713-8fgf-76c3-q08j-573561122245-W720H1280\"],\"category\":1,\"title\":\"\",\"cert\":\"\",\"enableIdent\":1,\"liveShow\":1,\"userinfoId\":40277963,\"fee\":{\"goodshopServices\":{\"feeName\":\"\\u4f18\\u5e97\\u5546\\u5bb6 - \\u6280\\u672f\\u670d\\u52a1\\u8d39\",\"feeType\":\"goodshopServices\",\"fee\":4}},\"secCategory\":1010,\"tagId\":\"\",\"isFirstEnd\":1,\"isLiveSale\":1,\"content\":\"\\u4e1c\\u65b9\\u4e4b\\u73e0\\u4fdd11-16\"}","priceJson":"{\"delayTime\":10,\"endTime\":\"2019-11-12 20:21:57\",\"fixedPrice\":0,\"increase\":2,\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0}","systemBzjJson":"[\"187552\",\"853508\",\"4667417\",\"5027541\",\"21703632\",\"45031031\",\"46806713\"]"}';
        $oldData = '{"expressFee":"freePost","price":{"bidbzjLimit":0,"bidmoney":0,"delayTime":10,"endTime":"2019-11-12 20:21:57","fixedPrice":0,"increase":2,"referencePrice":0,"bidbzj":0},"openTime":1573561137,"profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"东方之珠保11-16\",\"enableIdent\":1,\"fee\":{\"goodshopServices\":{\"fee\":4,\"feeName\":\"优店商家 - 技术服务费\",\"feeType\":\"goodshopServices\"}},\"imgs\":[\"201911121yv0z713-8fgf-76c3-q08j-573561122245-W720H1280\"],\"isFirstEnd\":1,\"isLiveSale\":1,\"liveShow\":1,\"secCategory\":1010,\"tagId\":\"\",\"title\":\"\",\"userinfoId\":40277963}","systemBzj":["853508","5027541","187552","45031031","4667417","46806713","21703632"],"secCategory":1010,"uri":"1911122018glqfzt","status":"sale","type":0,"likes":0,"isDel":0,"delayPayTime":0,"id":2010523009,"profile":{"category":1,"secCategory":1010,"cert":"","fee":{"goodshopServices":{"feeType":"goodshopServices","fee":4,"feeName":"优店商家 - 技术服务费"}},"isLiveSale":1,"liveShow":1,"tagId":"","userinfoId":40277963,"imgs":["201911121yv0z713-8fgf-76c3-q08j-573561122245-W720H1280"],"isFirstEnd":1,"enableIdent":1,"title":"","content":"东方之珠保11-16"},"category":1,"createTime":1573561137,"userinfoId":40277963,"endTime":1573561317,"recommendTime":0,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":10,\"endTime\":\"2019-11-12 20:21:57\",\"fixedPrice\":0,\"increase\":2,\"referencePrice\":0}","multiWins":1,"views":0,"systemBzjJson":"[\"853508\",\"5027541\",\"187552\",\"45031031\",\"4667417\",\"46806713\",\"21703632\"]"}';
        $newData = json_decode($newData);
        $oldData = json_decode($oldData);
        $eq = DiffArray::transfer('', $oldData, function () use ($newData) {
            return $newData;
        });
        $this->assertEquals(true, $eq);
    }

    public function test_sortFieldContent3()
    {
        $newData = '{"2006178972":{"finishedTime":1573366470,"winUserinfoId":1491830,"type":0,"openTime":1572966218,"deliveryTime":1573092609,"win":{"nickname":"sprinting-周","id":660789438,"score":"5dbNbw8aj","createTime":"2019-11-06 00:36:19","type":0,"price":49,"userinfoId":1491830,"saleId":2006178972,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0"},"isRated":0,"category":1,"uri":"1911052303ldjeif","disputeTime":0,"endTime":1573052459,"pid":0,"paidTime":1573053591,"delayPayTime":1573225259,"status":"deliveryReturn","dispute":1,"isShare":1,"userinfoId":1418421,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉老型，品相如图，喇叭山料，包浆玉化老油绿！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","isShow":0,"delayReceiptTime":1573697409,"multiWins":1,"unsoldReason":"normal","price":{"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0},"draftId":913993229,"views":74,"enableReturn":2,"expressFee":"freePost","recommendTime":0,"createTime":1572966218,"isDel":0,"secCategory":1015,"id":2006178972,"profile":{"category":1,"secCategory":1015,"cert":"","title":"原矿瓷釉老型","withChainCodes":"","enableIdent":1,"imgs":["20191105Hcia7kzNgkRVWkETgggdGoimr46MmDUFSW3riziAIwCQABJRSz03HoHEdNbGOj2v-W1080H1080","20191105AMlhklutVB9U-xxL6rsq-VoriKrpNNJnZCTdkZC9m80uciGnxCVlode19FCxfzpg-W1080H1080","20191105F10RkKiSKHheXQQcEgJ6lih07DnfPZ2dbux_CJRoLRA8tVzErilj5wcLI5ifY5eW-W1080H1080","20191105KGBbFLfjZXaK_DVinl-6G8j_I9ewQJHlpJTSvhj8Q1P3ab5iUJ_AH-mfYTMVj9do-W1080H1080","20191105i5TbudRXURxtVifMkSg0eip7xzqtOi2n6-eE31seX5zaCA-b6ZmZX10DeWiQFW6E-W1080H1080","20191105PH8w92VZfkgQ1rNgZzgCKIaKqTK_1eA5iqzPAqOq4t_3ev9k2ID5Nzw30bZtny0b-W1080H1080","20191105HDw-hQnmK-nMYgCJP8Ts3abO7PupABBa6K2QnHaJU0xionVW-DeubpNhdhymm6U3-W1080H1080","201911059s4xcaLlStoJAdz2VpPi2hWBrItTaY-Um8qIWJhvK1hZk8sNg4QE3KR867a-aSEO-W1080H1080","20191105e9sbF-YUtpykSCjjuYSprleEi0MAfG0q6Z_W6_gpmW3nYcxh5avE34m8nmONqDie-W1080H1080"],"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"typeName":"产地","value":"竹山"},{"typeName":"规格","value":"11×11"},{"typeName":"重量(g)","value":"1.91"},{"typeName":"矿口","value":"喇叭山"},{"value":"","typeName":"雕刻师"},{"typeName":"题材","value":""}],"tagId":"171","userinfoId":1418421,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉老型，品相如图，喇叭山料，包浆玉化老油绿！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"likes":10,"systemBzj":null,"launchTime":1573394300,"goodsId":913993229,"profileJson":"{\"category\":1,\"secCategory\":1015,\"cert\":\"\",\"title\":\"\\u539f\\u77ff\\u74f7\\u91c9\\u8001\\u578b\",\"withChainCodes\":\"\",\"enableIdent\":1,\"imgs\":[\"20191105Hcia7kzNgkRVWkETgggdGoimr46MmDUFSW3riziAIwCQABJRSz03HoHEdNbGOj2v-W1080H1080\",\"20191105AMlhklutVB9U-xxL6rsq-VoriKrpNNJnZCTdkZC9m80uciGnxCVlode19FCxfzpg-W1080H1080\",\"20191105F10RkKiSKHheXQQcEgJ6lih07DnfPZ2dbux_CJRoLRA8tVzErilj5wcLI5ifY5eW-W1080H1080\",\"20191105KGBbFLfjZXaK_DVinl-6G8j_I9ewQJHlpJTSvhj8Q1P3ab5iUJ_AH-mfYTMVj9do-W1080H1080\",\"20191105i5TbudRXURxtVifMkSg0eip7xzqtOi2n6-eE31seX5zaCA-b6ZmZX10DeWiQFW6E-W1080H1080\",\"20191105PH8w92VZfkgQ1rNgZzgCKIaKqTK_1eA5iqzPAqOq4t_3ev9k2ID5Nzw30bZtny0b-W1080H1080\",\"20191105HDw-hQnmK-nMYgCJP8Ts3abO7PupABBa6K2QnHaJU0xionVW-DeubpNhdhymm6U3-W1080H1080\",\"201911059s4xcaLlStoJAdz2VpPi2hWBrItTaY-Um8qIWJhvK1hZk8sNg4QE3KR867a-aSEO-W1080H1080\",\"20191105e9sbF-YUtpykSCjjuYSprleEi0MAfG0q6Z_W6_gpmW3nYcxh5avE34m8nmONqDie-W1080H1080\"],\"secCategoryTemplate\":[{\"typeName\":\"\\u7c7b\\u522b\",\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\"},{\"typeName\":\"\\u6837\\u5f0f\",\"value\":\"\\u73e0\\u5b50\\\/\\u73e0\\u4e32\"},{\"typeName\":\"\\u4ea7\\u5730\",\"value\":\"\\u7af9\\u5c71\"},{\"typeName\":\"\\u89c4\\u683c\",\"value\":\"11\\u00d711\"},{\"typeName\":\"\\u91cd\\u91cf(g)\",\"value\":\"1.91\"},{\"typeName\":\"\\u77ff\\u53e3\",\"value\":\"\\u5587\\u53ed\\u5c71\"},{\"value\":\"\",\"typeName\":\"\\u96d5\\u523b\\u5e08\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\"}],\"tagId\":\"171\",\"userinfoId\":1418421,\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u74f7\\u91c9\\u8001\\u578b\\uff0c\\u54c1\\u76f8\\u5982\\u56fe\\uff0c\\u5587\\u53ed\\u5c71\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u8001\\u6cb9\\u7eff\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u5706\\u901a\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a23\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"nickname\":\"sprinting-\\u5468\",\"id\":660789438,\"score\":\"5dbNbw8aj\",\"createTime\":\"2019-11-06 00:36:19\",\"type\":0,\"price\":49,\"userinfoId\":1491830,\"saleId\":2006178972,\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\"}","priceJson":"{\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0}","systemBzjJson":""},"2006183577":{"launchTime":1573392437,"delayReceiptTime":1573697409,"uri":"1911052306xkr3dj","id":2006183577,"openTime":1572966389,"unsoldReason":"normal","secCategory":1015,"isShow":0,"finishedTime":1573366463,"isRated":0,"deliveryTime":1573092609,"userinfoId":1418421,"profile":{"secCategory":1015,"title":"原矿高瓷龙珠","cert":"","imgs":["20191105xgK2_2XCyuIMeCS0BMfykHlodu48i7gz4MjXA0F_lbaFiQ6Pw0c1tkF6zmdBXAnG-W1080H1080","20191105cu_ZeArH70pjvb2ntahmFzrljcS9kOn-GRoIm0sum-AAmL_z6YpDLkasaga6KCbg-W1080H1080","20191105c1fA50FBWse6t8NogLruritnZVavD4LiPy14ep-zD-rRQiTDNIpi-aAuVUdUTv1j-W1080H1080","20191105FIjR4B_fZvgL324XF3CeGAk81Oq006JXQ6sqXWwvBcuS06k7bhTzNY5ha4zb1aUN-W1080H1080","20191105q2Okfiu3c05lkPOGfYLgaBqZX9J2QEg3_DUbCTxaLljFFCXXULf7q0E7BGMmQ44v-W1080H1080","20191105WaiuAKMkPRJY-GNN1VlBqhAlnoYONdjYCtaajZmxxSfi9vTlGgd3zTj5Rm8jPjbe-W1080H1080","20191105pD2_d7JBiYHuDkQFlq-ZHgE00iUhZXumL7JXOwcwdtODLV0v7iLXod27j_lTSg-B-W1080H1080","201911056px1Ge7iG18hOzRXnt0AiuDH3J-KWkPnUHQK6ilYDG1ie-2SZDi9fxx98MQiA-x9-W1080H1080"],"category":1,"tagId":"171","video":"o_1572961675508hbsAwOvWtYTSyR","secCategoryTemplate":[{"value":"原矿绿松石","typeName":"类别"},{"value":"珠子\/珠串","typeName":"样式"},{"typeName":"产地","value":"竹山"},{"typeName":"规格","value":"15.5"},{"typeName":"重量(g)","value":"3.84"},{"typeName":"矿口","value":"秦古"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"userinfoId":1418421,"videoOrg":"o_1572961675508hbsAwOvWtYTSyR.quicktime","withChainCodes":"","enableIdent":1,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷龙珠，品相如有，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"type":0,"isShare":2,"status":"deliveryReturn","views":226,"winUserinfoId":1491830,"systemBzj":null,"likes":13,"draftId":914010028,"price":{"fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59"},"paidTime":1573053616,"delayPayTime":1573225259,"pid":0,"win":{"userinfoId":1491830,"score":"5dbQESUkg","saleId":2006183577,"price":343,"createTime":"2019-11-06 19:53:07","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","type":0,"id":661280527,"nickname":"sprinting-周"},"recommendTime":0,"endTime":1573052459,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷龙珠，品相如有，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","disputeTime":0,"isDel":0,"dispute":1,"enableReturn":2,"expressFee":"freePost","multiWins":1,"category":1,"createTime":1572966389,"goodsId":914010028,"profileJson":"{\"secCategory\":1015,\"title\":\"\\u539f\\u77ff\\u9ad8\\u74f7\\u9f99\\u73e0\",\"cert\":\"\",\"imgs\":[\"20191105xgK2_2XCyuIMeCS0BMfykHlodu48i7gz4MjXA0F_lbaFiQ6Pw0c1tkF6zmdBXAnG-W1080H1080\",\"20191105cu_ZeArH70pjvb2ntahmFzrljcS9kOn-GRoIm0sum-AAmL_z6YpDLkasaga6KCbg-W1080H1080\",\"20191105c1fA50FBWse6t8NogLruritnZVavD4LiPy14ep-zD-rRQiTDNIpi-aAuVUdUTv1j-W1080H1080\",\"20191105FIjR4B_fZvgL324XF3CeGAk81Oq006JXQ6sqXWwvBcuS06k7bhTzNY5ha4zb1aUN-W1080H1080\",\"20191105q2Okfiu3c05lkPOGfYLgaBqZX9J2QEg3_DUbCTxaLljFFCXXULf7q0E7BGMmQ44v-W1080H1080\",\"20191105WaiuAKMkPRJY-GNN1VlBqhAlnoYONdjYCtaajZmxxSfi9vTlGgd3zTj5Rm8jPjbe-W1080H1080\",\"20191105pD2_d7JBiYHuDkQFlq-ZHgE00iUhZXumL7JXOwcwdtODLV0v7iLXod27j_lTSg-B-W1080H1080\",\"201911056px1Ge7iG18hOzRXnt0AiuDH3J-KWkPnUHQK6ilYDG1ie-2SZDi9fxx98MQiA-x9-W1080H1080\"],\"category\":1,\"tagId\":\"171\",\"video\":\"o_1572961675508hbsAwOvWtYTSyR\",\"secCategoryTemplate\":[{\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\",\"typeName\":\"\\u7c7b\\u522b\"},{\"value\":\"\\u73e0\\u5b50\\\/\\u73e0\\u4e32\",\"typeName\":\"\\u6837\\u5f0f\"},{\"typeName\":\"\\u4ea7\\u5730\",\"value\":\"\\u7af9\\u5c71\"},{\"typeName\":\"\\u89c4\\u683c\",\"value\":\"15.5\"},{\"typeName\":\"\\u91cd\\u91cf(g)\",\"value\":\"3.84\"},{\"typeName\":\"\\u77ff\\u53e3\",\"value\":\"\\u79e6\\u53e4\"},{\"typeName\":\"\\u96d5\\u523b\\u5e08\",\"value\":\"\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\"}],\"userinfoId\":1418421,\"videoOrg\":\"o_1572961675508hbsAwOvWtYTSyR.quicktime\",\"withChainCodes\":\"\",\"enableIdent\":1,\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u9ad8\\u74f7\\u9f99\\u73e0\\uff0c\\u54c1\\u76f8\\u5982\\u6709\\uff0c\\u79e6\\u53e4\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u6cb9\\u6da6\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u5706\\u901a\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a23\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"userinfoId\":1491830,\"score\":\"5dbQESUkg\",\"saleId\":2006183577,\"price\":343,\"createTime\":\"2019-11-06 19:53:07\",\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"type\":0,\"id\":661280527,\"nickname\":\"sprinting-\\u5468\"}","priceJson":"{\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\"}","systemBzjJson":""},"2006183739":{"recommendTime":0,"launchTime":1573554840,"deliveryTime":1573092761,"winUserinfoId":20202806,"profile":{"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"挂件\/把件\/摆件"},{"value":"竹山","typeName":"产地"},{"typeName":"规格","value":"19.5×10"},{"typeName":"重量(g)","value":"5.1"},{"value":"秦古","typeName":"矿口"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"tagId":"168","category":1,"cert":"","secCategory":1015,"userinfoId":1418421,"enableIdent":1,"imgs":["20191105BLO4qcxN3jbCZJS0uu3wBITg3N6X3naMhaeET1Mu0h0SMU3d4XcCiNTChUJkUiRK-W1080H1080","20191105sucfwK92Vm6qVKGtJzsZEi4MohR4dQk9hrAJzOcB8PeG_SeLL1Pflb4w65j8tWDD-W1080H1080","20191105wJOaTCxVquc4_IA6BWVFLCZmu3jetl664KDj8JYvmw26oJ60ea7ReJPDdk8nZhI7-W1080H1080","20191105vY9B1H9lG9uOHCloy_mrPNS9SFpn9jrdqNtbRPWEdYzn-OlpszsoLcU9RlF3DxxY-W1080H1080","20191105n8o1NuMvhcNgHWzI_OCojQEMYDCEPwBIV_l6iHXY5gNORtBYSFs7xnbQxs9fIbli-W1080H1080","20191105Ynha1ruZqV2pcF8nM0olXDp_p1edyef2Fg9_mejooNQGepLtYyu8J1Td7dEc69sz-W1080H1080","20191105WaY55cCLX3_muVyXFEXR3qXMkMKkNYOMqYoovWktTKgOTOJv4XJgwswNOoNSbyrA-W1080H1080","20191105xFWcf05d9gDV5f_X-iICBHPDlvM43yjkb0340WOa3TLDcnAErY0WcQeT0yz9HjBw-W1080H1080"],"withChainCodes":"","video":"o_1572961772387ASTIjNrrdXa1ogD","title":"原矿高瓷龙扣","videoOrg":"o_1572961772387ASTIjNrrdXa1ogD.quicktime","content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷精工龙扣，无坑无裂，秦古高瓷果冻料，包浆玉化油润！微白可盘掉！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"systemBzj":["15857078","31529843","33669929"],"type":0,"finishedTime":0,"id":2006183739,"delayReceiptTime":1574302361,"endTime":1573052759,"paidTime":1573053182,"pid":0,"win":{"createTime":"2019-11-06 22:58:55","price":554,"nickname":"剛子","type":0,"id":661581124,"userinfoId":20202806,"score":"5dbTajaY1","saleId":2006183739,"headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/20191030GPyw0pGicibl6IGu8noaGP0UTFia2hbncnGeoG1pGtCAOJIH8SfZMlia9wibuBmE2nhTrpeMqiabUQ4LqiaWAQNKsSnGA\/132"},"openTime":1572966394,"multiWins":1,"dispute":1,"createTime":1572966394,"status":"deliveryReturn","isDel":0,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷精工龙扣，无坑无裂，秦古高瓷果冻料，包浆玉化油润！微白可盘掉！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","isShow":0,"uri":"1911052306ovqkdv","likes":20,"views":182,"price":{"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0},"isShare":2,"delayPayTime":1573225559,"userinfoId":1418421,"expressFee":"freePost","category":1,"secCategory":1015,"enableReturn":1,"isRated":0,"draftId":914011213,"unsoldReason":"normal","disputeTime":0,"goodsId":914011213,"profileJson":"{\"secCategoryTemplate\":[{\"typeName\":\"\\u7c7b\\u522b\",\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\"},{\"typeName\":\"\\u6837\\u5f0f\",\"value\":\"\\u6302\\u4ef6\\\/\\u628a\\u4ef6\\\/\\u6446\\u4ef6\"},{\"value\":\"\\u7af9\\u5c71\",\"typeName\":\"\\u4ea7\\u5730\"},{\"typeName\":\"\\u89c4\\u683c\",\"value\":\"19.5\\u00d710\"},{\"typeName\":\"\\u91cd\\u91cf(g)\",\"value\":\"5.1\"},{\"value\":\"\\u79e6\\u53e4\",\"typeName\":\"\\u77ff\\u53e3\"},{\"typeName\":\"\\u96d5\\u523b\\u5e08\",\"value\":\"\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\"}],\"tagId\":\"168\",\"category\":1,\"cert\":\"\",\"secCategory\":1015,\"userinfoId\":1418421,\"enableIdent\":1,\"imgs\":[\"20191105BLO4qcxN3jbCZJS0uu3wBITg3N6X3naMhaeET1Mu0h0SMU3d4XcCiNTChUJkUiRK-W1080H1080\",\"20191105sucfwK92Vm6qVKGtJzsZEi4MohR4dQk9hrAJzOcB8PeG_SeLL1Pflb4w65j8tWDD-W1080H1080\",\"20191105wJOaTCxVquc4_IA6BWVFLCZmu3jetl664KDj8JYvmw26oJ60ea7ReJPDdk8nZhI7-W1080H1080\",\"20191105vY9B1H9lG9uOHCloy_mrPNS9SFpn9jrdqNtbRPWEdYzn-OlpszsoLcU9RlF3DxxY-W1080H1080\",\"20191105n8o1NuMvhcNgHWzI_OCojQEMYDCEPwBIV_l6iHXY5gNORtBYSFs7xnbQxs9fIbli-W1080H1080\",\"20191105Ynha1ruZqV2pcF8nM0olXDp_p1edyef2Fg9_mejooNQGepLtYyu8J1Td7dEc69sz-W1080H1080\",\"20191105WaY55cCLX3_muVyXFEXR3qXMkMKkNYOMqYoovWktTKgOTOJv4XJgwswNOoNSbyrA-W1080H1080\",\"20191105xFWcf05d9gDV5f_X-iICBHPDlvM43yjkb0340WOa3TLDcnAErY0WcQeT0yz9HjBw-W1080H1080\"],\"withChainCodes\":\"\",\"video\":\"o_1572961772387ASTIjNrrdXa1ogD\",\"title\":\"\\u539f\\u77ff\\u9ad8\\u74f7\\u9f99\\u6263\",\"videoOrg\":\"o_1572961772387ASTIjNrrdXa1ogD.quicktime\",\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u9ad8\\u74f7\\u7cbe\\u5de5\\u9f99\\u6263\\uff0c\\u65e0\\u5751\\u65e0\\u88c2\\uff0c\\u79e6\\u53e4\\u9ad8\\u74f7\\u679c\\u51bb\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u6cb9\\u6da6\\uff01\\u5fae\\u767d\\u53ef\\u76d8\\u6389\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u5706\\u901a\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a23\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"createTime\":\"2019-11-06 22:58:55\",\"price\":554,\"nickname\":\"\\u525b\\u5b50\",\"type\":0,\"id\":661581124,\"userinfoId\":20202806,\"score\":\"5dbTajaY1\",\"saleId\":2006183739,\"headimgurl\":\"http:\\\/\\\/thirdwx.qlogo.cn\\\/mmopen\\\/20191030GPyw0pGicibl6IGu8noaGP0UTFia2hbncnGeoG1pGtCAOJIH8SfZMlia9wibuBmE2nhTrpeMqiabUQ4LqiaWAQNKsSnGA\\\/132\"}","priceJson":"{\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0,\"bidbzj\":0}","systemBzjJson":"[\"15857078\",\"31529843\",\"33669929\"]"},"2006179172":{"category":1,"unsoldReason":"normal","isShow":0,"recommendTime":0,"uri":"1911052303c1wzyi","winUserinfoId":1491830,"multiWins":1,"userinfoId":1418421,"profile":{"imgs":["20191105lprZdQfR9mjBfCfuPVVd2RiFnOKw6MfI9ZvtLf_vMR2xcNFOoidxzBGsj6-1sap3-W1080H1080","20191105vDF29fbuQz-YkCcYwAO-w2W4k68pajDpTGerGQjikPgwLUSWQLyRigSmN-QXNrQp-W1080H1080","20191105Ynha1ruZqV2pcF8nM0olXGC4X5SfRKt9JKacO5Omzi9QnlI8FOmox3w6iM51Huf0-W1080H1080","20191105VMjcLw_elZorSiGVGKAuE2F_yy_LTQWOrT8_nInN-bUGSLwJyNsEbBbjRYwcST_A-W1080H1080","20191105k_63_Ym8Mp5iky_D8c-7-jB5WVjrsvwggTM0i1ddNiY-iK9e1_5vxhAd0AVYFM6w-W1080H1080","20191105Dz5N57dbHzdYLA-Ka_zGopmMKq88F-Y8WZDyrStLqUWuvPcEPC8e_GNGaMlSvJIU-W1080H1080","201911055r-sRa9bvvVPmU5N3Yo7AjifRV_9V6yjgcVgJEIoHiX1Z2nWA8Qp6OIUiKki2A8U-W1080H1080"],"withChainCodes":"","secCategory":1015,"tagId":"171","secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"value":"竹山","typeName":"产地"},{"typeName":"规格","value":"10+"},{"typeName":"重量(g)","value":"1.62"},{"typeName":"矿口","value":"秦古"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"category":1,"userinfoId":1418421,"cert":"","enableIdent":1,"title":"原矿高瓷回纹","content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷回纹珠，无坑无裂，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"type":0,"enableReturn":2,"isShare":1,"secCategory":1015,"dispute":1,"pid":0,"finishedTime":1573366467,"delayReceiptTime":1573697409,"price":{"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300},"views":110,"launchTime":1573392420,"endTime":1573052459,"isRated":0,"likes":11,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷回纹珠，无坑无裂，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","win":{"nickname":"sprinting-周","createTime":"2019-11-06 00:35:49","score":"5dbNbw83M","id":660789040,"price":49,"userinfoId":1491830,"type":0,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","saleId":2006179172},"deliveryTime":1573092609,"status":"deliveryReturn","createTime":1572966227,"systemBzj":null,"disputeTime":0,"expressFee":"freePost","openTime":1572966227,"delayPayTime":1573225259,"draftId":913972707,"paidTime":1573053557,"isDel":0,"id":2006179172,"goodsId":913972707,"profileJson":"{\"imgs\":[\"20191105lprZdQfR9mjBfCfuPVVd2RiFnOKw6MfI9ZvtLf_vMR2xcNFOoidxzBGsj6-1sap3-W1080H1080\",\"20191105vDF29fbuQz-YkCcYwAO-w2W4k68pajDpTGerGQjikPgwLUSWQLyRigSmN-QXNrQp-W1080H1080\",\"20191105Ynha1ruZqV2pcF8nM0olXGC4X5SfRKt9JKacO5Omzi9QnlI8FOmox3w6iM51Huf0-W1080H1080\",\"20191105VMjcLw_elZorSiGVGKAuE2F_yy_LTQWOrT8_nInN-bUGSLwJyNsEbBbjRYwcST_A-W1080H1080\",\"20191105k_63_Ym8Mp5iky_D8c-7-jB5WVjrsvwggTM0i1ddNiY-iK9e1_5vxhAd0AVYFM6w-W1080H1080\",\"20191105Dz5N57dbHzdYLA-Ka_zGopmMKq88F-Y8WZDyrStLqUWuvPcEPC8e_GNGaMlSvJIU-W1080H1080\",\"201911055r-sRa9bvvVPmU5N3Yo7AjifRV_9V6yjgcVgJEIoHiX1Z2nWA8Qp6OIUiKki2A8U-W1080H1080\"],\"withChainCodes\":\"\",\"secCategory\":1015,\"tagId\":\"171\",\"secCategoryTemplate\":[{\"typeName\":\"\\u7c7b\\u522b\",\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\"},{\"typeName\":\"\\u6837\\u5f0f\",\"value\":\"\\u73e0\\u5b50\\\/\\u73e0\\u4e32\"},{\"value\":\"\\u7af9\\u5c71\",\"typeName\":\"\\u4ea7\\u5730\"},{\"typeName\":\"\\u89c4\\u683c\",\"value\":\"10+\"},{\"typeName\":\"\\u91cd\\u91cf(g)\",\"value\":\"1.62\"},{\"typeName\":\"\\u77ff\\u53e3\",\"value\":\"\\u79e6\\u53e4\"},{\"typeName\":\"\\u96d5\\u523b\\u5e08\",\"value\":\"\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\"}],\"category\":1,\"userinfoId\":1418421,\"cert\":\"\",\"enableIdent\":1,\"title\":\"\\u539f\\u77ff\\u9ad8\\u74f7\\u56de\\u7eb9\",\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u9ad8\\u74f7\\u56de\\u7eb9\\u73e0\\uff0c\\u65e0\\u5751\\u65e0\\u88c2\\uff0c\\u79e6\\u53e4\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u6cb9\\u6da6\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u5706\\u901a\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a23\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"nickname\":\"sprinting-\\u5468\",\"createTime\":\"2019-11-06 00:35:49\",\"score\":\"5dbNbw83M\",\"id\":660789040,\"price\":49,\"userinfoId\":1491830,\"type\":0,\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"saleId\":2006179172}","priceJson":"{\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300}","systemBzjJson":""},"2004933726":{"systemBzj":null,"finishedTime":1573366478,"delayReceiptTime":1573697409,"likes":0,"endTime":1572966059,"launchTime":1573392506,"deliveryTime":1573092609,"isShare":2,"id":2004933726,"enableReturn":2,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷弥勒，品相如图，洞子沟天空蓝料，包浆玉化蓝！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","winUserinfoId":1491830,"status":"deliveryReturn","views":0,"uri":"1911032305f7z77o","multiWins":1,"pid":0,"isDel":0,"secCategory":1015,"price":{"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-05 23:00:59","fixedPrice":0,"increase":45},"draftId":913077006,"recommendTime":0,"dispute":1,"disputeTime":0,"profile":{"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"typeName":"产地","value":"竹山"},{"value":"12.5","typeName":"规格"},{"typeName":"重量(g)","value":"1.9"},{"typeName":"矿口","value":"洞子沟"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"title":"原矿高瓷弥勒","userinfoId":1418421,"withChainCodes":"","category":1,"enableIdent":1,"imgs":["20191103ewQwl0j7I-WD-lK_pzuLDqcV3qSZjBvKTbhV3LpZW28LDtXiXxYVbB-ZywV3_7md-W1080H1080","20191103qox3_eBFRbqJC62P4Hk5GmHHULmeQmZJ8T1qENcRVRmkUYreKQotN8UCjS-2qn7b-W1080H1080","20191103QmaeiCVv6rIEctISkfNI04HmeYOSKsNZftrXPYDUCba2eM4A1DNvA37UGvLtaH43-W1080H1080","20191103FEtSPGN19X5nDkhjZN1p9IOxqEviRp4hwfHw43d8-AngcqYBtFN8cySXaKkYGftL-W1080H1080","20191103Dz5N57dbHzdYLA-Ka_zGomQRUSU7pDqSkndnmJOIPKPhBkCQylQyu0wu6mE-awYE-W1080H1080","20191103DORbSncnwcibauwqhgXf915c_oA_aqKh7bEaAfdT4FafhmdGBDlbV2OT3cW_LaV2-W1080H1080","20191103bhrrUKKZLJSNbm7kF6FCzyU9v2oA9jPhYiH3a2R4DYF1eLlgyDzjCGk5XkxRxuFO-W1080H1080","20191103kJUc8T2RxZm5GRuM7Dkj_YM1_oahZz23rNSC9TrzhXqlozUWHuhbCEjr8a4uUz4a-W1080H1080","20191103iqlCEtd-J9R8aqpSrwWz1G1mnMqWrXdKDyvMFRUdk8nPOacLn_AKW2nUAnP2hheJ-W1080H1080"],"secCategory":1015,"tagId":"171","cert":"","content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷弥勒，品相如图，洞子沟天空蓝料，包浆玉化蓝！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"isRated":0,"delayPayTime":1573138859,"expressFee":"freePost","type":0,"openTime":1572793513,"win":{"id":659104753,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","price":90,"score":"5dbNFJ5iq","type":0,"nickname":"sprinting-周","userinfoId":1491830,"saleId":2004933726,"createTime":"2019-11-04 07:10:32"},"unsoldReason":"normal","userinfoId":1418421,"category":1,"createTime":1572793513,"isShow":0,"paidTime":1573011050,"goodsId":913077006,"profileJson":"{\"secCategoryTemplate\":[{\"typeName\":\"\\u7c7b\\u522b\",\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\"},{\"typeName\":\"\\u6837\\u5f0f\",\"value\":\"\\u73e0\\u5b50\\\/\\u73e0\\u4e32\"},{\"typeName\":\"\\u4ea7\\u5730\",\"value\":\"\\u7af9\\u5c71\"},{\"value\":\"12.5\",\"typeName\":\"\\u89c4\\u683c\"},{\"typeName\":\"\\u91cd\\u91cf(g)\",\"value\":\"1.9\"},{\"typeName\":\"\\u77ff\\u53e3\",\"value\":\"\\u6d1e\\u5b50\\u6c9f\"},{\"typeName\":\"\\u96d5\\u523b\\u5e08\",\"value\":\"\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\"}],\"title\":\"\\u539f\\u77ff\\u9ad8\\u74f7\\u5f25\\u52d2\",\"userinfoId\":1418421,\"withChainCodes\":\"\",\"category\":1,\"enableIdent\":1,\"imgs\":[\"20191103ewQwl0j7I-WD-lK_pzuLDqcV3qSZjBvKTbhV3LpZW28LDtXiXxYVbB-ZywV3_7md-W1080H1080\",\"20191103qox3_eBFRbqJC62P4Hk5GmHHULmeQmZJ8T1qENcRVRmkUYreKQotN8UCjS-2qn7b-W1080H1080\",\"20191103QmaeiCVv6rIEctISkfNI04HmeYOSKsNZftrXPYDUCba2eM4A1DNvA37UGvLtaH43-W1080H1080\",\"20191103FEtSPGN19X5nDkhjZN1p9IOxqEviRp4hwfHw43d8-AngcqYBtFN8cySXaKkYGftL-W1080H1080\",\"20191103Dz5N57dbHzdYLA-Ka_zGomQRUSU7pDqSkndnmJOIPKPhBkCQylQyu0wu6mE-awYE-W1080H1080\",\"20191103DORbSncnwcibauwqhgXf915c_oA_aqKh7bEaAfdT4FafhmdGBDlbV2OT3cW_LaV2-W1080H1080\",\"20191103bhrrUKKZLJSNbm7kF6FCzyU9v2oA9jPhYiH3a2R4DYF1eLlgyDzjCGk5XkxRxuFO-W1080H1080\",\"20191103kJUc8T2RxZm5GRuM7Dkj_YM1_oahZz23rNSC9TrzhXqlozUWHuhbCEjr8a4uUz4a-W1080H1080\",\"20191103iqlCEtd-J9R8aqpSrwWz1G1mnMqWrXdKDyvMFRUdk8nPOacLn_AKW2nUAnP2hheJ-W1080H1080\"],\"secCategory\":1015,\"tagId\":\"171\",\"cert\":\"\",\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u9ad8\\u74f7\\u5f25\\u52d2\\uff0c\\u54c1\\u76f8\\u5982\\u56fe\\uff0c\\u6d1e\\u5b50\\u6c9f\\u5929\\u7a7a\\u84dd\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u84dd\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u5706\\u901a\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a23\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"id\":659104753,\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"price\":90,\"score\":\"5dbNFJ5iq\",\"type\":0,\"nickname\":\"sprinting-\\u5468\",\"userinfoId\":1491830,\"saleId\":2004933726,\"createTime\":\"2019-11-04 07:10:32\"}","priceJson":"{\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-05 23:00:59\",\"fixedPrice\":0,\"increase\":45}","systemBzjJson":""},"2004936081":{"isDel":0,"systemBzj":["950877"],"uri":"19110323062p0uda","category":1,"status":"deliveryReturn","dispute":1,"launchTime":1573475030,"delayReceiptTime":1574214773,"type":0,"createTime":1572793600,"unsoldReason":"normal","multiWins":1,"pid":0,"isRated":0,"views":0,"winUserinfoId":2621589,"openTime":1572793600,"finishedTime":0,"delayPayTime":1573138859,"id":2004936081,"deliveryTime":1573005173,"recommendTime":0,"profile":{"enableIdent":1,"video":"o_1569760534193rJFjra8b93YdHoh","category":1,"cert":"","title":"原矿瓷釉陨石坑三通","videoOrg":"o_1569760534193rJFjra8b93YdHoh","withChainCodes":"","imgs":["20190929dJA06vs9__J68KPdwqvHrUHDeGKgVA305lHZYUE0pEdfC-50GfJRwB4IaC5uOuUd-W1080H1080","20190929X-527uIZrdmZRrkn6gPlUKDmxpPJYbQYKHecZNXfiz8fdB6jgXie22_gbY9J7KWn-W1080H1080","20190929cZjbMLbkg6RnNz84SblX-7KuVAv5V-lISBSjB2zHHqFVBrXGpi-qTNmb6pV7oqOG-W1080H1080","20190929piz1wg_Yqge-mbxltNWIRQJGrFTlADYk5dIE7H5ngamTn3iDETHm6FKdl-u7DUPH-W1080H1080","20190929BziuNfZYDfv_mR2gsjqk7EhP8SD5ZXbB41IGEOMuqBzR73WurJ4xzNrLroA1M_qS-W1080H1080","20190929WF4NIeQqjV2w2ELK--WsDadHbz28p-CqVroChpFGSYhUXAuIiwTvYchI0Bh-JuVj-W1080H1080","20190929DclmE9zq48Wjpx2f1Twn4fmyLSazKJBxj5PE8ex1W7U80c8HwZtYXgdFRhGSZ6IZ-W1080H1080","20190929r9GmsNTwWJ9TqTwCqE2M623drIZa8t9A5kSLkzi8hvIJDK9jeqxciOlYT-ErdDZE-W1080H1080"],"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"value":"竹山","typeName":"产地"},{"value":"27×16.5×15mm","typeName":"规格"},{"value":"7.66克","typeName":"重量(g)"},{"typeName":"矿口","value":"秦古"},{"value":"","typeName":"雕刻师"},{"typeName":"题材","value":"三通"}],"tagId":"171","userinfoId":1418421,"secCategory":1015,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉陨石坑三通，品相如图，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【百世快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上22点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！"},"isShare":2,"endTime":1572966059,"draftId":904526538,"userinfoId":1418421,"win":{"saleId":2004936081,"userinfoId":2621589,"nickname":"A【々卐★】.谦","price":528,"createTime":"2019-11-05 16:54:52","type":0,"headimgurl":"http:\/\/appwpt-10002380.image.myqcloud.com\/2017122992dc83ae-00b0-43c2-8eec-0532369b6237","id":660263907,"score":"5dbSQqTid"},"secCategory":1015,"isShow":0,"price":{"bidmoney":0,"delayTime":300,"endTime":"2019-11-05 23:00:59","fixedPrice":0,"increase":66,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0},"disputeTime":0,"expressFee":"freePost","paidTime":1572967894,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉陨石坑三通，品相如图，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【百世快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上22点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","likes":0,"enableReturn":1,"goodsId":904526538,"profileJson":"{\"enableIdent\":1,\"video\":\"o_1569760534193rJFjra8b93YdHoh\",\"category\":1,\"cert\":\"\",\"title\":\"\\u539f\\u77ff\\u74f7\\u91c9\\u9668\\u77f3\\u5751\\u4e09\\u901a\",\"videoOrg\":\"o_1569760534193rJFjra8b93YdHoh\",\"withChainCodes\":\"\",\"imgs\":[\"20190929dJA06vs9__J68KPdwqvHrUHDeGKgVA305lHZYUE0pEdfC-50GfJRwB4IaC5uOuUd-W1080H1080\",\"20190929X-527uIZrdmZRrkn6gPlUKDmxpPJYbQYKHecZNXfiz8fdB6jgXie22_gbY9J7KWn-W1080H1080\",\"20190929cZjbMLbkg6RnNz84SblX-7KuVAv5V-lISBSjB2zHHqFVBrXGpi-qTNmb6pV7oqOG-W1080H1080\",\"20190929piz1wg_Yqge-mbxltNWIRQJGrFTlADYk5dIE7H5ngamTn3iDETHm6FKdl-u7DUPH-W1080H1080\",\"20190929BziuNfZYDfv_mR2gsjqk7EhP8SD5ZXbB41IGEOMuqBzR73WurJ4xzNrLroA1M_qS-W1080H1080\",\"20190929WF4NIeQqjV2w2ELK--WsDadHbz28p-CqVroChpFGSYhUXAuIiwTvYchI0Bh-JuVj-W1080H1080\",\"20190929DclmE9zq48Wjpx2f1Twn4fmyLSazKJBxj5PE8ex1W7U80c8HwZtYXgdFRhGSZ6IZ-W1080H1080\",\"20190929r9GmsNTwWJ9TqTwCqE2M623drIZa8t9A5kSLkzi8hvIJDK9jeqxciOlYT-ErdDZE-W1080H1080\"],\"secCategoryTemplate\":[{\"typeName\":\"\\u7c7b\\u522b\",\"value\":\"\\u539f\\u77ff\\u7eff\\u677e\\u77f3\"},{\"typeName\":\"\\u6837\\u5f0f\",\"value\":\"\\u73e0\\u5b50\\\/\\u73e0\\u4e32\"},{\"value\":\"\\u7af9\\u5c71\",\"typeName\":\"\\u4ea7\\u5730\"},{\"value\":\"27\\u00d716.5\\u00d715mm\",\"typeName\":\"\\u89c4\\u683c\"},{\"value\":\"7.66\\u514b\",\"typeName\":\"\\u91cd\\u91cf(g)\"},{\"typeName\":\"\\u77ff\\u53e3\",\"value\":\"\\u79e6\\u53e4\"},{\"value\":\"\",\"typeName\":\"\\u96d5\\u523b\\u5e08\"},{\"typeName\":\"\\u9898\\u6750\",\"value\":\"\\u4e09\\u901a\"}],\"tagId\":\"171\",\"userinfoId\":1418421,\"secCategory\":1015,\"content\":\"V6\\u8001\\u94fa\\uff01\\u4ea7\\u5730\\u76f4\\u9500\\uff01\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\u539f\\u77ff\\uff01\\n\\n\\u3010\\u4ea7\\u54c1\\u63cf\\u8ff0\\u3011\\uff1a\\u539f\\u77ff\\u74f7\\u91c9\\u9668\\u77f3\\u5751\\u4e09\\u901a\\uff0c\\u54c1\\u76f8\\u5982\\u56fe\\uff0c\\u79e6\\u53e4\\u6599\\uff0c\\u5305\\u6d46\\u7389\\u5316\\u6cb9\\u6da6\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff01\\u77ff\\u533a\\u6709\\u5b9e\\u4f53\\u5e97\\u94fa\\uff01\\u54c1\\u8d28\\u6709\\u4fdd\\u969c\\uff01\\n\\n\\u5168\\u573a\\u677e\\u77f30\\u5143\\u8d77\\u62cd\\uff0c\\u5305\\u90ae\\uff0c\\u5305\\u9000\\uff0c\\u4fdd\\u771f\\uff0c\\u652f\\u6301\\u65e0\\u7406\\u7531\\u9000\\u8d27\\uff01\\u62cd\\u591a\\u5c11\\u7b97\\u591a\\u5c11\\uff0c\\u4e00\\u7269\\u4e00\\u56fe\\uff0c\\u54c1\\u8d28\\u4fe1\\u8a89\\u6709\\u4fdd\\u8bc1\\uff0c\\u8bf7\\u653e\\u5fc3\\u51fa\\u4ef7\\uff01\\n\\u3010\\u5feb\\u9012\\u3011\\u9ed8\\u8ba4\\u3010\\u767e\\u4e16\\u5feb\\u9012\\u5305\\u90ae\\u3011\\uff01\\u652f\\u6301\\u66f4\\u6539\\u5feb\\u9012\\uff01\\n\\n\\u6bcf\\u5929\\u4e0a\\u62cd\\u7ed3\\u675f\\u62cd\\u5356\\u65f6\\u95f4\\u665a\\u4e0a22\\u70b9\\u6574\\uff0c\\u5230\\u8d27\\u4e0d\\u6ee1\\u610f\\u4e0d\\u8981\\u52a8\\u7eff\\u677e\\u77f3\\uff0c\\u5e73\\u53f0\\u9000\\u56de\\u5373\\u53ef\\uff01\\n\\n\\u5fae\\u62cd\\u5802\\u4f01\\u4e1a\\u8ba4\\u8bc1\\uff0c\\u5b9e\\u4f53\\u5e97\\u94fa\\u4f4d\\u4e8e\\u7eff\\u677e\\u77f3\\u53d1\\u6e90\\u5730\\uff0c\\u6e56\\u5317\\u7701\\u5341\\u5830\\u5e02\\u7af9\\u5c71\\u53bf\\u9ebb\\u5bb6\\u6e21\\u9547\\uff01\\u5bb6\\u91cc\\u4e00\\u76f4\\u7ecf\\u8425\\u7eff\\u677e\\u77f3\\u51e0\\u5341\\u5e74\\uff0c\\u51e0\\u4ee3\\u4eba\\u4e00\\u76f4\\u4ee5\\u54c1\\u8d28\\u81f3\\u4e0a\\uff0c\\u5320\\u5fc3\\u00b7\\u4f20\\u627f\\u4e3a\\u6838\\u5fc3\\uff0c\\u4e00\\u624b\\u8d27\\u6e90\\u4ef7\\u683c\\u54c1\\u8d28\\u90fd\\u662f\\u53ef\\u4ee5\\u5bf9\\u6bd4\\u7684\\uff0c\\u8ba9\\u5404\\u4f4d\\u670b\\u53cb\\u4e70\\u7684\\u653e\\u5fc3\\uff0c\\u4e70\\u7684\\u5f00\\u5fc3\\u3002\\u5fae\\u62cd\\u4e13\\u6ce8\\u4e8e\\u539f\\u77ff\\u7eff\\u677e\\u77f3\\u62cd\\u5356\\u4e13\\u4e00\\u4e13\\u4e1a\\uff0c\\u54c1\\u8d28\\u4fdd\\u8bc1\\uff0c\\u652f\\u6301\\u7eff\\u677e\\u77f3\\u4ea7\\u54c1\\u52a0\\u5de5\\uff0c\\u6279\\u53d1\\uff0c\\u5b9a\\u5236\\uff01\\u4efb\\u4f55\\u4e0d\\u61c2\\u4ee5\\u53ca\\u95ee\\u9898\\u53ef\\u4ee5\\u7559\\u8a00\\u627e\\u6211\\uff01\\u6b22\\u8fce\\u6765\\u4ea7\\u5730\\u8f6c\\u8f6c\\u5b9e\\u4f53\\u5e97\\u770b\\u7eff\\u677e\\u77f3\\uff01\"}","winJson":"{\"saleId\":2004936081,\"userinfoId\":2621589,\"nickname\":\"A\\u3010\\u3005\\u5350\\u2605\\u3011.\\u8c26\",\"price\":528,\"createTime\":\"2019-11-05 16:54:52\",\"type\":0,\"headimgurl\":\"http:\\\/\\\/appwpt-10002380.image.myqcloud.com\\\/2017122992dc83ae-00b0-43c2-8eec-0532369b6237\",\"id\":660263907,\"score\":\"5dbSQqTid\"}","priceJson":"{\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-05 23:00:59\",\"fixedPrice\":0,\"increase\":66,\"referencePrice\":0,\"bidbzj\":0,\"bidbzjLimit\":0}","systemBzjJson":"[\"950877\"]"}}';
        $oldData = '{"2006183739":{"paidTime":1573053182,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0}","multiWins":1,"win":{"price":554,"headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/20191030GPyw0pGicibl6IGu8noaGP0UTFia2hbncnGeoG1pGtCAOJIH8SfZMlia9wibuBmE2nhTrpeMqiabUQ4LqiaWAQNKsSnGA\/132","id":661581124,"nickname":"剛子","saleId":2006183739,"score":"5dbTajaY1","type":0,"userinfoId":20202806,"createTime":"2019-11-06 22:58:55"},"likes":0,"delayReceiptTime":1574302361,"endTime":1573052759,"goodsId":914011213,"delayPayTime":1573225559,"type":0,"id":2006183739,"userinfoId":1418421,"uri":"1911052306ovqkdv","dispute":1,"profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿高瓷精工龙扣，无坑无裂，秦古高瓷果冻料，包浆玉化油润！微白可盘掉！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【圆通快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20191105BLO4qcxN3jbCZJS0uu3wBITg3N6X3naMhaeET1Mu0h0SMU3d4XcCiNTChUJkUiRK-W1080H1080\",\"20191105sucfwK92Vm6qVKGtJzsZEi4MohR4dQk9hrAJzOcB8PeG_SeLL1Pflb4w65j8tWDD-W1080H1080\",\"20191105wJOaTCxVquc4_IA6BWVFLCZmu3jetl664KDj8JYvmw26oJ60ea7ReJPDdk8nZhI7-W1080H1080\",\"20191105vY9B1H9lG9uOHCloy_mrPNS9SFpn9jrdqNtbRPWEdYzn-OlpszsoLcU9RlF3DxxY-W1080H1080\",\"20191105n8o1NuMvhcNgHWzI_OCojQEMYDCEPwBIV_l6iHXY5gNORtBYSFs7xnbQxs9fIbli-W1080H1080\",\"20191105Ynha1ruZqV2pcF8nM0olXDp_p1edyef2Fg9_mejooNQGepLtYyu8J1Td7dEc69sz-W1080H1080\",\"20191105WaY55cCLX3_muVyXFEXR3qXMkMKkNYOMqYoovWktTKgOTOJv4XJgwswNOoNSbyrA-W1080H1080\",\"20191105xFWcf05d9gDV5f_X-iICBHPDlvM43yjkb0340WOa3TLDcnAErY0WcQeT0yz9HjBw-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"挂件\/把件\/摆件\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"19.5×10\"},{\"typeName\":\"重量(g)\",\"value\":\"5.1\"},{\"typeName\":\"矿口\",\"value\":\"秦古\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"\"}],\"tagId\":\"168\",\"title\":\"原矿高瓷龙扣\",\"userinfoId\":1418421,\"video\":\"o_1572961772387ASTIjNrrdXa1ogD\",\"videoOrg\":\"o_1572961772387ASTIjNrrdXa1ogD.quicktime\",\"withChainCodes\":\"\"}","expressFee":"freePost","winJson":"{\"id\":661581124,\"price\":554,\"type\":0,\"score\":\"5dbTajaY1\",\"userinfoId\":20202806,\"nickname\":\"剛子\",\"createTime\":\"2019-11-06 22:58:55\",\"saleId\":2006183739,\"headimgurl\":\"http:\\\/\\\/thirdwx.qlogo.cn\\\/mmopen\\\/20191030GPyw0pGicibl6IGu8noaGP0UTFia2hbncnGeoG1pGtCAOJIH8SfZMlia9wibuBmE2nhTrpeMqiabUQ4LqiaWAQNKsSnGA\\\/132\"}","views":0,"unsoldReason":"normal","isShare":2,"winUserinfoId":20202806,"secCategory":1015,"profile":{"withChainCodes":"","content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷精工龙扣，无坑无裂，秦古高瓷果冻料，包浆玉化油润！微白可盘掉！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","imgs":["20191105BLO4qcxN3jbCZJS0uu3wBITg3N6X3naMhaeET1Mu0h0SMU3d4XcCiNTChUJkUiRK-W1080H1080","20191105sucfwK92Vm6qVKGtJzsZEi4MohR4dQk9hrAJzOcB8PeG_SeLL1Pflb4w65j8tWDD-W1080H1080","20191105wJOaTCxVquc4_IA6BWVFLCZmu3jetl664KDj8JYvmw26oJ60ea7ReJPDdk8nZhI7-W1080H1080","20191105vY9B1H9lG9uOHCloy_mrPNS9SFpn9jrdqNtbRPWEdYzn-OlpszsoLcU9RlF3DxxY-W1080H1080","20191105n8o1NuMvhcNgHWzI_OCojQEMYDCEPwBIV_l6iHXY5gNORtBYSFs7xnbQxs9fIbli-W1080H1080","20191105Ynha1ruZqV2pcF8nM0olXDp_p1edyef2Fg9_mejooNQGepLtYyu8J1Td7dEc69sz-W1080H1080","20191105WaY55cCLX3_muVyXFEXR3qXMkMKkNYOMqYoovWktTKgOTOJv4XJgwswNOoNSbyrA-W1080H1080","20191105xFWcf05d9gDV5f_X-iICBHPDlvM43yjkb0340WOa3TLDcnAErY0WcQeT0yz9HjBw-W1080H1080"],"enableIdent":1,"title":"原矿高瓷龙扣","video":"o_1572961772387ASTIjNrrdXa1ogD","videoOrg":"o_1572961772387ASTIjNrrdXa1ogD.quicktime","tagId":"168","secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"value":"挂件\/把件\/摆件","typeName":"样式"},{"typeName":"产地","value":"竹山"},{"value":"19.5×10","typeName":"规格"},{"value":"5.1","typeName":"重量(g)"},{"typeName":"矿口","value":"秦古"},{"typeName":"雕刻师","value":""},{"value":"","typeName":"题材"}],"userinfoId":1418421,"category":1,"cert":"","secCategory":1015},"enableReturn":1,"launchTime":1573554840,"openTime":1572966394,"pid":0,"systemBzj":["33669929","31529843","15857078"],"deliveryTime":1573092761,"handicraft":154840652,"recommendTime":0,"disputeTime":0,"finishedTime":0,"createTime":1572966394,"category":1,"price":{"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0},"isShow":0,"systemBzjJson":"[\"33669929\",\"31529843\",\"15857078\"]","status":"deliveryReturn","isRated":0,"isDel":0},"2004936081":{"winUserinfoId":2621589,"status":"deliveryReturn","enableReturn":1,"endTime":1572966059,"uri":"19110323062p0uda","systemBzj":[950877],"handicraft":175030008,"category":1,"isRated":0,"createTime":1572793600,"price":{"bidmoney":0,"delayTime":300,"endTime":"2019-11-05 23:00:59","fixedPrice":0,"increase":66,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0},"isShare":2,"type":0,"paidTime":1572967894,"recommendTime":0,"isShow":0,"openTime":1572793600,"winJson":"{\"type\":0,\"price\":528,\"headimgurl\":\"http:\\\/\\\/appwpt-10002380.image.myqcloud.com\\\/2017122992dc83ae-00b0-43c2-8eec-0532369b6237\",\"createTime\":\"2019-11-05 16:54:52\",\"saleId\":2004936081,\"id\":660263907,\"nickname\":\"A【々卐★】.谦\",\"userinfoId\":2621589,\"score\":\"5dbSQqTid\"}","multiWins":1,"goodsId":904526538,"launchTime":1573475030,"dispute":1,"win":{"createTime":"2019-11-05 16:54:52","saleId":2004936081,"type":0,"userinfoId":2621589,"score":"5dbSQqTid","price":528,"id":660263907,"nickname":"A【々卐★】.谦","headimgurl":"http:\/\/appwpt-10002380.image.myqcloud.com\/2017122992dc83ae-00b0-43c2-8eec-0532369b6237"},"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-05 23:00:59\",\"fixedPrice\":0,\"increase\":66,\"referencePrice\":0}","delayPayTime":1573138859,"expressFee":"freePost","profile":{"cert":"","enableIdent":1,"imgs":["20190929dJA06vs9__J68KPdwqvHrUHDeGKgVA305lHZYUE0pEdfC-50GfJRwB4IaC5uOuUd-W1080H1080","20190929X-527uIZrdmZRrkn6gPlUKDmxpPJYbQYKHecZNXfiz8fdB6jgXie22_gbY9J7KWn-W1080H1080","20190929cZjbMLbkg6RnNz84SblX-7KuVAv5V-lISBSjB2zHHqFVBrXGpi-qTNmb6pV7oqOG-W1080H1080","20190929piz1wg_Yqge-mbxltNWIRQJGrFTlADYk5dIE7H5ngamTn3iDETHm6FKdl-u7DUPH-W1080H1080","20190929BziuNfZYDfv_mR2gsjqk7EhP8SD5ZXbB41IGEOMuqBzR73WurJ4xzNrLroA1M_qS-W1080H1080","20190929WF4NIeQqjV2w2ELK--WsDadHbz28p-CqVroChpFGSYhUXAuIiwTvYchI0Bh-JuVj-W1080H1080","20190929DclmE9zq48Wjpx2f1Twn4fmyLSazKJBxj5PE8ex1W7U80c8HwZtYXgdFRhGSZ6IZ-W1080H1080","20190929r9GmsNTwWJ9TqTwCqE2M623drIZa8t9A5kSLkzi8hvIJDK9jeqxciOlYT-ErdDZE-W1080H1080"],"secCategory":1015,"category":1,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉陨石坑三通，品相如图，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【百世快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上22点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"value":"竹山","typeName":"产地"},{"typeName":"规格","value":"27×16.5×15mm"},{"typeName":"重量(g)","value":"7.66克"},{"typeName":"矿口","value":"秦古"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":"三通"}],"userinfoId":1418421,"title":"原矿瓷釉陨石坑三通","video":"o_1569760534193rJFjra8b93YdHoh","withChainCodes":"","tagId":"171","videoOrg":"o_1569760534193rJFjra8b93YdHoh"},"disputeTime":0,"views":0,"pid":0,"finishedTime":0,"deliveryTime":1573005173,"unsoldReason":"normal","likes":0,"secCategory":1015,"isDel":0,"profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿瓷釉陨石坑三通，品相如图，秦古料，包浆玉化油润！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【百世快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上22点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20190929dJA06vs9__J68KPdwqvHrUHDeGKgVA305lHZYUE0pEdfC-50GfJRwB4IaC5uOuUd-W1080H1080\",\"20190929X-527uIZrdmZRrkn6gPlUKDmxpPJYbQYKHecZNXfiz8fdB6jgXie22_gbY9J7KWn-W1080H1080\",\"20190929cZjbMLbkg6RnNz84SblX-7KuVAv5V-lISBSjB2zHHqFVBrXGpi-qTNmb6pV7oqOG-W1080H1080\",\"20190929piz1wg_Yqge-mbxltNWIRQJGrFTlADYk5dIE7H5ngamTn3iDETHm6FKdl-u7DUPH-W1080H1080\",\"20190929BziuNfZYDfv_mR2gsjqk7EhP8SD5ZXbB41IGEOMuqBzR73WurJ4xzNrLroA1M_qS-W1080H1080\",\"20190929WF4NIeQqjV2w2ELK--WsDadHbz28p-CqVroChpFGSYhUXAuIiwTvYchI0Bh-JuVj-W1080H1080\",\"20190929DclmE9zq48Wjpx2f1Twn4fmyLSazKJBxj5PE8ex1W7U80c8HwZtYXgdFRhGSZ6IZ-W1080H1080\",\"20190929r9GmsNTwWJ9TqTwCqE2M623drIZa8t9A5kSLkzi8hvIJDK9jeqxciOlYT-ErdDZE-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"珠子\/珠串\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"27×16.5×15mm\"},{\"typeName\":\"重量(g)\",\"value\":\"7.66克\"},{\"typeName\":\"矿口\",\"value\":\"秦古\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"三通\"}],\"tagId\":\"171\",\"title\":\"原矿瓷釉陨石坑三通\",\"userinfoId\":1418421,\"video\":\"o_1569760534193rJFjra8b93YdHoh\",\"videoOrg\":\"o_1569760534193rJFjra8b93YdHoh\",\"withChainCodes\":\"\"}","delayReceiptTime":1574214773,"systemBzjJson":"[950877]","userinfoId":1418421,"id":2004936081},"2004933726":{"delayReceiptTime":1573697409,"expressFee":"freePost","dispute":1,"isShare":2,"winJson":"{\"type\":0,\"nickname\":\"sprinting-周\",\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"createTime\":\"2019-11-04 07:10:32\",\"id\":659104753,\"price\":90,\"userinfoId\":1491830,\"saleId\":2004933726,\"score\":\"5dbNFJ5iq\"}","uri":"1911032305f7z77o","finishedTime":1573366478,"id":2004933726,"isRated":0,"multiWins":1,"delayPayTime":1573138859,"systemBzjJson":"","profile":{"title":"原矿高瓷弥勒","withChainCodes":"","category":1,"enableIdent":1,"secCategory":1015,"userinfoId":1418421,"cert":"","imgs":["20191103ewQwl0j7I-WD-lK_pzuLDqcV3qSZjBvKTbhV3LpZW28LDtXiXxYVbB-ZywV3_7md-W1080H1080","20191103qox3_eBFRbqJC62P4Hk5GmHHULmeQmZJ8T1qENcRVRmkUYreKQotN8UCjS-2qn7b-W1080H1080","20191103QmaeiCVv6rIEctISkfNI04HmeYOSKsNZftrXPYDUCba2eM4A1DNvA37UGvLtaH43-W1080H1080","20191103FEtSPGN19X5nDkhjZN1p9IOxqEviRp4hwfHw43d8-AngcqYBtFN8cySXaKkYGftL-W1080H1080","20191103Dz5N57dbHzdYLA-Ka_zGomQRUSU7pDqSkndnmJOIPKPhBkCQylQyu0wu6mE-awYE-W1080H1080","20191103DORbSncnwcibauwqhgXf915c_oA_aqKh7bEaAfdT4FafhmdGBDlbV2OT3cW_LaV2-W1080H1080","20191103bhrrUKKZLJSNbm7kF6FCzyU9v2oA9jPhYiH3a2R4DYF1eLlgyDzjCGk5XkxRxuFO-W1080H1080","20191103kJUc8T2RxZm5GRuM7Dkj_YM1_oahZz23rNSC9TrzhXqlozUWHuhbCEjr8a4uUz4a-W1080H1080","20191103iqlCEtd-J9R8aqpSrwWz1G1mnMqWrXdKDyvMFRUdk8nPOacLn_AKW2nUAnP2hheJ-W1080H1080"],"secCategoryTemplate":[{"value":"原矿绿松石","typeName":"类别"},{"value":"珠子\/珠串","typeName":"样式"},{"typeName":"产地","value":"竹山"},{"typeName":"规格","value":"12.5"},{"typeName":"重量(g)","value":"1.9"},{"typeName":"矿口","value":"洞子沟"},{"typeName":"雕刻师","value":""},{"value":"","typeName":"题材"}],"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷弥勒，品相如图，洞子沟天空蓝料，包浆玉化蓝！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","tagId":"171"},"enableReturn":2,"isShow":0,"secCategory":1015,"isDel":0,"deliveryTime":1573092609,"disputeTime":0,"recommendTime":0,"handicraft":192506701,"views":0,"price":{"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-05 23:00:59","fixedPrice":0,"increase":45,"referencePrice":0},"profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿高瓷弥勒，品相如图，洞子沟天空蓝料，包浆玉化蓝！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【圆通快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20191103ewQwl0j7I-WD-lK_pzuLDqcV3qSZjBvKTbhV3LpZW28LDtXiXxYVbB-ZywV3_7md-W1080H1080\",\"20191103qox3_eBFRbqJC62P4Hk5GmHHULmeQmZJ8T1qENcRVRmkUYreKQotN8UCjS-2qn7b-W1080H1080\",\"20191103QmaeiCVv6rIEctISkfNI04HmeYOSKsNZftrXPYDUCba2eM4A1DNvA37UGvLtaH43-W1080H1080\",\"20191103FEtSPGN19X5nDkhjZN1p9IOxqEviRp4hwfHw43d8-AngcqYBtFN8cySXaKkYGftL-W1080H1080\",\"20191103Dz5N57dbHzdYLA-Ka_zGomQRUSU7pDqSkndnmJOIPKPhBkCQylQyu0wu6mE-awYE-W1080H1080\",\"20191103DORbSncnwcibauwqhgXf915c_oA_aqKh7bEaAfdT4FafhmdGBDlbV2OT3cW_LaV2-W1080H1080\",\"20191103bhrrUKKZLJSNbm7kF6FCzyU9v2oA9jPhYiH3a2R4DYF1eLlgyDzjCGk5XkxRxuFO-W1080H1080\",\"20191103kJUc8T2RxZm5GRuM7Dkj_YM1_oahZz23rNSC9TrzhXqlozUWHuhbCEjr8a4uUz4a-W1080H1080\",\"20191103iqlCEtd-J9R8aqpSrwWz1G1mnMqWrXdKDyvMFRUdk8nPOacLn_AKW2nUAnP2hheJ-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"珠子\/珠串\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"12.5\"},{\"typeName\":\"重量(g)\",\"value\":\"1.9\"},{\"typeName\":\"矿口\",\"value\":\"洞子沟\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"\"}],\"tagId\":\"171\",\"title\":\"原矿高瓷弥勒\",\"userinfoId\":1418421,\"withChainCodes\":\"\"}","category":1,"endTime":1572966059,"openTime":1572793513,"status":"deliveryReturn","likes":0,"createTime":1572793513,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-05 23:00:59\",\"fixedPrice\":0,\"increase\":45,\"referencePrice\":0}","unsoldReason":"normal","launchTime":1573392506,"winUserinfoId":1491830,"win":{"createTime":"2019-11-04 07:10:32","price":90,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","id":659104753,"type":0,"nickname":"sprinting-周","userinfoId":1491830,"saleId":2004933726,"score":"5dbNFJ5iq"},"systemBzj":"","userinfoId":1418421,"paidTime":1573011050,"goodsId":913077006,"pid":0,"type":0},"2006178972":{"secCategory":1015,"price":{"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300},"dispute":1,"isShow":0,"finishedTime":1573366470,"likes":0,"createTime":1572966218,"uri":"1911052303ldjeif","deliveryTime":1573092609,"disputeTime":0,"id":2006178972,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0}","systemBzjJson":"","enableReturn":2,"pid":0,"profile":{"enableIdent":1,"cert":"","userinfoId":1418421,"withChainCodes":"","category":1,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿瓷釉老型，品相如图，喇叭山料，包浆玉化老油绿！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","secCategory":1015,"secCategoryTemplate":[{"value":"原矿绿松石","typeName":"类别"},{"typeName":"样式","value":"珠子\/珠串"},{"typeName":"产地","value":"竹山"},{"typeName":"规格","value":"11×11"},{"typeName":"重量(g)","value":"1.91"},{"typeName":"矿口","value":"喇叭山"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"tagId":"171","title":"原矿瓷釉老型","imgs":["20191105Hcia7kzNgkRVWkETgggdGoimr46MmDUFSW3riziAIwCQABJRSz03HoHEdNbGOj2v-W1080H1080","20191105AMlhklutVB9U-xxL6rsq-VoriKrpNNJnZCTdkZC9m80uciGnxCVlode19FCxfzpg-W1080H1080","20191105F10RkKiSKHheXQQcEgJ6lih07DnfPZ2dbux_CJRoLRA8tVzErilj5wcLI5ifY5eW-W1080H1080","20191105KGBbFLfjZXaK_DVinl-6G8j_I9ewQJHlpJTSvhj8Q1P3ab5iUJ_AH-mfYTMVj9do-W1080H1080","20191105i5TbudRXURxtVifMkSg0eip7xzqtOi2n6-eE31seX5zaCA-b6ZmZX10DeWiQFW6E-W1080H1080","20191105PH8w92VZfkgQ1rNgZzgCKIaKqTK_1eA5iqzPAqOq4t_3ev9k2ID5Nzw30bZtny0b-W1080H1080","20191105HDw-hQnmK-nMYgCJP8Ts3abO7PupABBa6K2QnHaJU0xionVW-DeubpNhdhymm6U3-W1080H1080","201911059s4xcaLlStoJAdz2VpPi2hWBrItTaY-Um8qIWJhvK1hZk8sNg4QE3KR867a-aSEO-W1080H1080","20191105e9sbF-YUtpykSCjjuYSprleEi0MAfG0q6Z_W6_gpmW3nYcxh5avE34m8nmONqDie-W1080H1080"]},"paidTime":1573053591,"isShare":1,"goodsId":913993229,"unsoldReason":"normal","delayReceiptTime":1573697409,"systemBzj":"","status":"deliveryReturn","expressFee":"freePost","openTime":1572966218,"isDel":0,"launchTime":1573394300,"recommendTime":0,"type":0,"category":1,"winUserinfoId":1491830,"userinfoId":1418421,"delayPayTime":1573225259,"winJson":"{\"score\":\"5dbNbw8aj\",\"createTime\":\"2019-11-06 00:36:19\",\"type\":0,\"nickname\":\"sprinting-周\",\"saleId\":2006178972,\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"id\":660789438,\"price\":49,\"userinfoId\":1491830}","multiWins":1,"views":0,"profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿瓷釉老型，品相如图，喇叭山料，包浆玉化老油绿！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【圆通快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20191105Hcia7kzNgkRVWkETgggdGoimr46MmDUFSW3riziAIwCQABJRSz03HoHEdNbGOj2v-W1080H1080\",\"20191105AMlhklutVB9U-xxL6rsq-VoriKrpNNJnZCTdkZC9m80uciGnxCVlode19FCxfzpg-W1080H1080\",\"20191105F10RkKiSKHheXQQcEgJ6lih07DnfPZ2dbux_CJRoLRA8tVzErilj5wcLI5ifY5eW-W1080H1080\",\"20191105KGBbFLfjZXaK_DVinl-6G8j_I9ewQJHlpJTSvhj8Q1P3ab5iUJ_AH-mfYTMVj9do-W1080H1080\",\"20191105i5TbudRXURxtVifMkSg0eip7xzqtOi2n6-eE31seX5zaCA-b6ZmZX10DeWiQFW6E-W1080H1080\",\"20191105PH8w92VZfkgQ1rNgZzgCKIaKqTK_1eA5iqzPAqOq4t_3ev9k2ID5Nzw30bZtny0b-W1080H1080\",\"20191105HDw-hQnmK-nMYgCJP8Ts3abO7PupABBa6K2QnHaJU0xionVW-DeubpNhdhymm6U3-W1080H1080\",\"201911059s4xcaLlStoJAdz2VpPi2hWBrItTaY-Um8qIWJhvK1hZk8sNg4QE3KR867a-aSEO-W1080H1080\",\"20191105e9sbF-YUtpykSCjjuYSprleEi0MAfG0q6Z_W6_gpmW3nYcxh5avE34m8nmONqDie-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"珠子\/珠串\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"11×11\"},{\"typeName\":\"重量(g)\",\"value\":\"1.91\"},{\"typeName\":\"矿口\",\"value\":\"喇叭山\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"\"}],\"tagId\":\"171\",\"title\":\"原矿瓷釉老型\",\"userinfoId\":1418421,\"withChainCodes\":\"\"}","handicraft":194300183,"win":{"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","price":49,"createTime":"2019-11-06 00:36:19","id":660789438,"score":"5dbNbw8aj","saleId":2006178972,"type":0,"nickname":"sprinting-周","userinfoId":1491830},"endTime":1573052459,"isRated":0},"2006179172":{"winUserinfoId":1491830,"type":0,"paidTime":1573053557,"endTime":1573052459,"status":"deliveryReturn","finishedTime":1573366467,"delayReceiptTime":1573697409,"isDel":0,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0}","profile":{"withChainCodes":"","category":1,"imgs":["20191105lprZdQfR9mjBfCfuPVVd2RiFnOKw6MfI9ZvtLf_vMR2xcNFOoidxzBGsj6-1sap3-W1080H1080","20191105vDF29fbuQz-YkCcYwAO-w2W4k68pajDpTGerGQjikPgwLUSWQLyRigSmN-QXNrQp-W1080H1080","20191105Ynha1ruZqV2pcF8nM0olXGC4X5SfRKt9JKacO5Omzi9QnlI8FOmox3w6iM51Huf0-W1080H1080","20191105VMjcLw_elZorSiGVGKAuE2F_yy_LTQWOrT8_nInN-bUGSLwJyNsEbBbjRYwcST_A-W1080H1080","20191105k_63_Ym8Mp5iky_D8c-7-jB5WVjrsvwggTM0i1ddNiY-iK9e1_5vxhAd0AVYFM6w-W1080H1080","20191105Dz5N57dbHzdYLA-Ka_zGopmMKq88F-Y8WZDyrStLqUWuvPcEPC8e_GNGaMlSvJIU-W1080H1080","201911055r-sRa9bvvVPmU5N3Yo7AjifRV_9V6yjgcVgJEIoHiX1Z2nWA8Qp6OIUiKki2A8U-W1080H1080"],"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"typeName":"样式","value":"珠子\/珠串"},{"typeName":"产地","value":"竹山"},{"value":"10+","typeName":"规格"},{"value":"1.62","typeName":"重量(g)"},{"typeName":"矿口","value":"秦古"},{"typeName":"雕刻师","value":""},{"typeName":"题材","value":""}],"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷回纹珠，无坑无裂，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","secCategory":1015,"tagId":"171","title":"原矿高瓷回纹","enableIdent":1,"userinfoId":1418421,"cert":""},"isShare":1,"recommendTime":0,"delayPayTime":1573225259,"dispute":1,"isRated":0,"goodsId":913972707,"winJson":"{\"userinfoId\":1491830,\"createTime\":\"2019-11-06 00:35:49\",\"type\":0,\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"score\":\"5dbNbw83M\",\"id\":660789040,\"saleId\":2006179172,\"nickname\":\"sprinting-周\",\"price\":49}","systemBzjJson":"","userinfoId":1418421,"openTime":1572966227,"secCategory":1015,"launchTime":1573392420,"likes":0,"handicraft":192420482,"disputeTime":0,"pid":0,"unsoldReason":"normal","win":{"saleId":2006179172,"score":"5dbNbw83M","price":49,"id":660789040,"userinfoId":1491830,"type":0,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","createTime":"2019-11-06 00:35:49","nickname":"sprinting-周"},"views":0,"createTime":1572966227,"id":2006179172,"uri":"1911052303c1wzyi","profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿高瓷回纹珠，无坑无裂，秦古料，包浆玉化油润！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【圆通快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20191105lprZdQfR9mjBfCfuPVVd2RiFnOKw6MfI9ZvtLf_vMR2xcNFOoidxzBGsj6-1sap3-W1080H1080\",\"20191105vDF29fbuQz-YkCcYwAO-w2W4k68pajDpTGerGQjikPgwLUSWQLyRigSmN-QXNrQp-W1080H1080\",\"20191105Ynha1ruZqV2pcF8nM0olXGC4X5SfRKt9JKacO5Omzi9QnlI8FOmox3w6iM51Huf0-W1080H1080\",\"20191105VMjcLw_elZorSiGVGKAuE2F_yy_LTQWOrT8_nInN-bUGSLwJyNsEbBbjRYwcST_A-W1080H1080\",\"20191105k_63_Ym8Mp5iky_D8c-7-jB5WVjrsvwggTM0i1ddNiY-iK9e1_5vxhAd0AVYFM6w-W1080H1080\",\"20191105Dz5N57dbHzdYLA-Ka_zGopmMKq88F-Y8WZDyrStLqUWuvPcEPC8e_GNGaMlSvJIU-W1080H1080\",\"201911055r-sRa9bvvVPmU5N3Yo7AjifRV_9V6yjgcVgJEIoHiX1Z2nWA8Qp6OIUiKki2A8U-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"珠子\/珠串\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"10+\"},{\"typeName\":\"重量(g)\",\"value\":\"1.62\"},{\"typeName\":\"矿口\",\"value\":\"秦古\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"\"}],\"tagId\":\"171\",\"title\":\"原矿高瓷回纹\",\"userinfoId\":1418421,\"withChainCodes\":\"\"}","category":1,"enableReturn":2,"expressFee":"freePost","multiWins":1,"price":{"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0,"bidbzj":0},"deliveryTime":1573092609,"isShow":0,"systemBzj":""},"2006183577":{"goodsId":914010028,"isRated":0,"delayPayTime":1573225259,"openTime":1572966389,"status":"deliveryReturn","paidTime":1573053616,"pid":0,"unsoldReason":"normal","systemBzj":"","type":0,"systemBzjJson":"","isShare":2,"enableReturn":2,"finishedTime":1573366463,"winUserinfoId":1491830,"winJson":"{\"id\":661280527,\"userinfoId\":1491830,\"createTime\":\"2019-11-06 19:53:07\",\"headimgurl\":\"http:\\\/\\\/wx.qlogo.cn\\\/mmopen\\\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\\\/0\",\"type\":0,\"score\":\"5dbQESUkg\",\"saleId\":2006183577,\"price\":343,\"nickname\":\"sprinting-周\"}","multiWins":1,"expressFee":"freePost","profileJson":"{\"category\":1,\"cert\":\"\",\"content\":\"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\\n\\n【产品描述】：原矿高瓷龙珠，品相如有，秦古料，包浆玉化油润！\\n\\n微拍堂企业认证！矿区有实体店铺！品质有保障！\\n\\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\\n【快递】默认【圆通快递包邮】！支持更改快递！\\n\\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\\n\\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！\",\"enableIdent\":1,\"imgs\":[\"20191105xgK2_2XCyuIMeCS0BMfykHlodu48i7gz4MjXA0F_lbaFiQ6Pw0c1tkF6zmdBXAnG-W1080H1080\",\"20191105cu_ZeArH70pjvb2ntahmFzrljcS9kOn-GRoIm0sum-AAmL_z6YpDLkasaga6KCbg-W1080H1080\",\"20191105c1fA50FBWse6t8NogLruritnZVavD4LiPy14ep-zD-rRQiTDNIpi-aAuVUdUTv1j-W1080H1080\",\"20191105FIjR4B_fZvgL324XF3CeGAk81Oq006JXQ6sqXWwvBcuS06k7bhTzNY5ha4zb1aUN-W1080H1080\",\"20191105q2Okfiu3c05lkPOGfYLgaBqZX9J2QEg3_DUbCTxaLljFFCXXULf7q0E7BGMmQ44v-W1080H1080\",\"20191105WaiuAKMkPRJY-GNN1VlBqhAlnoYONdjYCtaajZmxxSfi9vTlGgd3zTj5Rm8jPjbe-W1080H1080\",\"20191105pD2_d7JBiYHuDkQFlq-ZHgE00iUhZXumL7JXOwcwdtODLV0v7iLXod27j_lTSg-B-W1080H1080\",\"201911056px1Ge7iG18hOzRXnt0AiuDH3J-KWkPnUHQK6ilYDG1ie-2SZDi9fxx98MQiA-x9-W1080H1080\"],\"secCategory\":1015,\"secCategoryTemplate\":[{\"typeName\":\"类别\",\"value\":\"原矿绿松石\"},{\"typeName\":\"样式\",\"value\":\"珠子\/珠串\"},{\"typeName\":\"产地\",\"value\":\"竹山\"},{\"typeName\":\"规格\",\"value\":\"15.5\"},{\"typeName\":\"重量(g)\",\"value\":\"3.84\"},{\"typeName\":\"矿口\",\"value\":\"秦古\"},{\"typeName\":\"雕刻师\",\"value\":\"\"},{\"typeName\":\"题材\",\"value\":\"\"}],\"tagId\":\"171\",\"title\":\"原矿高瓷龙珠\",\"userinfoId\":1418421,\"video\":\"o_1572961675508hbsAwOvWtYTSyR\",\"videoOrg\":\"o_1572961675508hbsAwOvWtYTSyR.quicktime\",\"withChainCodes\":\"\"}","deliveryTime":1573092609,"isDel":0,"win":{"id":661280527,"type":0,"price":343,"userinfoId":1491830,"createTime":"2019-11-06 19:53:07","saleId":2006183577,"headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/20170417Q3auHgzwzM4luuIxia7MdJiaW8XPg5NF8lPEQvIxKdn0UeG7YNKBqa6wo7icKQLbLDPFDVyyxgOVY1v59gBnnoZ0Q\/0","score":"5dbQESUkg","nickname":"sprinting-周"},"dispute":1,"uri":"1911052306xkr3dj","disputeTime":0,"endTime":1573052459,"price":{"bidbzj":0,"bidbzjLimit":0,"bidmoney":0,"delayTime":300,"endTime":"2019-11-06 23:00:59","fixedPrice":0,"increase":49,"referencePrice":0},"createTime":1572966389,"handicraft":192437908,"category":1,"userinfoId":1418421,"profile":{"title":"原矿高瓷龙珠","videoOrg":"o_1572961675508hbsAwOvWtYTSyR.quicktime","category":1,"content":"V6老铺！产地直销！企业认证！原矿！原矿！原矿！\n\n【产品描述】：原矿高瓷龙珠，品相如有，秦古料，包浆玉化油润！\n\n微拍堂企业认证！矿区有实体店铺！品质有保障！\n\n全场松石0元起拍，包邮，包退，保真，支持无理由退货！拍多少算多少，一物一图，品质信誉有保证，请放心出价！\n【快递】默认【圆通快递包邮】！支持更改快递！\n\n每天上拍结束拍卖时间晚上23点整，到货不满意不要动绿松石，平台退回即可！\n\n微拍堂企业认证，实体店铺位于绿松石发源地，湖北省十堰市竹山县麻家渡镇！家里一直经营绿松石几十年，几代人一直以品质至上，匠心·传承为核心，一手货源价格品质都是可以对比的，让各位朋友买的放心，买的开心。微拍专注于原矿绿松石拍卖专一专业，品质保证，支持绿松石产品加工，批发，定制！任何不懂以及问题可以留言找我！欢迎来产地转转实体店看绿松石！","withChainCodes":"","cert":"","imgs":["20191105xgK2_2XCyuIMeCS0BMfykHlodu48i7gz4MjXA0F_lbaFiQ6Pw0c1tkF6zmdBXAnG-W1080H1080","20191105cu_ZeArH70pjvb2ntahmFzrljcS9kOn-GRoIm0sum-AAmL_z6YpDLkasaga6KCbg-W1080H1080","20191105c1fA50FBWse6t8NogLruritnZVavD4LiPy14ep-zD-rRQiTDNIpi-aAuVUdUTv1j-W1080H1080","20191105FIjR4B_fZvgL324XF3CeGAk81Oq006JXQ6sqXWwvBcuS06k7bhTzNY5ha4zb1aUN-W1080H1080","20191105q2Okfiu3c05lkPOGfYLgaBqZX9J2QEg3_DUbCTxaLljFFCXXULf7q0E7BGMmQ44v-W1080H1080","20191105WaiuAKMkPRJY-GNN1VlBqhAlnoYONdjYCtaajZmxxSfi9vTlGgd3zTj5Rm8jPjbe-W1080H1080","20191105pD2_d7JBiYHuDkQFlq-ZHgE00iUhZXumL7JXOwcwdtODLV0v7iLXod27j_lTSg-B-W1080H1080","201911056px1Ge7iG18hOzRXnt0AiuDH3J-KWkPnUHQK6ilYDG1ie-2SZDi9fxx98MQiA-x9-W1080H1080"],"secCategoryTemplate":[{"typeName":"类别","value":"原矿绿松石"},{"value":"珠子\/珠串","typeName":"样式"},{"value":"竹山","typeName":"产地"},{"typeName":"规格","value":"15.5"},{"value":"3.84","typeName":"重量(g)"},{"typeName":"矿口","value":"秦古"},{"value":"","typeName":"雕刻师"},{"typeName":"题材","value":""}],"tagId":"171","video":"o_1572961675508hbsAwOvWtYTSyR","enableIdent":1,"secCategory":1015,"userinfoId":1418421},"isShow":0,"views":0,"priceJson":"{\"bidbzj\":0,\"bidbzjLimit\":0,\"bidmoney\":0,\"delayTime\":300,\"endTime\":\"2019-11-06 23:00:59\",\"fixedPrice\":0,\"increase\":49,\"referencePrice\":0}","secCategory":1015,"launchTime":1573392437,"delayReceiptTime":1573697409,"recommendTime":0,"likes":0,"id":2006183577}}';
        $newData = json_decode($newData);
        $oldData = json_decode($oldData);
        $eq = DiffArray::transfer('', $oldData, function () use ($newData) {
            return $newData;
        });
        $this->assertEquals(true, $eq);
    }

    const PAY_TIME_END_SALE_TYPE = [0, 1, 4, 6, 7, 8, 9, 11, 12];

    public function test_LiveSaleLogic()
    {
        $pid = 2001419576;
        $fields = ['id', 'uri', 'status', 'paidTime', 'winUserinfoId'];
        $where = ['pid' => $pid];
        $saleMultiList = Sale::getAllSaleList($fields, $where);
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_pid', $saleMultiList, function () use ($pid) {
            $saleMultiList = Order::getOrderByPid($pid, [], ['saleId', 'winUserinfoId', 'snapshot', 'status', 'paidTime'], ['uri']);
            return $saleMultiList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    public function test_SaleMiniLogic()
    {
        $saleRow = new \stdClass();
        $saleRow->id = 2001419576;
        $saleMultiList = (new Sale())::getAllSaleList(
            ['id', 'uri', 'status', 'paidTime', 'winUserinfoId'],
            ['pid' => $saleRow->id], '', null, null, true
        );
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_pid', $saleMultiList, function () use ($saleRow) {
            $saleMultiList = Order::getOrderByPid($saleRow->id, [], ['saleId', 'winUserinfoId', 'snapshot', 'status', 'paidTime'], ['uri']);
            return $saleMultiList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_LiveLogic()
    {
        $userinfoId = 45681973;
        $saleId = 2001659704;
        $where = ['userinfoId' => $userinfoId, 'isDel' => 0, 'pid' => $saleId,
            'status' => 'deal', 'type' => 0];
        $multiWinsInfo = Sale::getAllSaleList(['uri', 'winJson', 'status', 'paidTime', 'winUserinfoId', 'id'], $where);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_pid', $multiWinsInfo, function () use ($userinfoId, $saleId) {
            $multiWinsInfo = Order::getOrderByPid($saleId, ['userinfoId' => $userinfoId, 'saleType' => 0, 'status' => 1], ['saleId', 'winUserinfoId', 'snapshot', 'status', 'paidTime', 'winJson'], ['uri']);
            return $multiWinsInfo;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_SaleLogic()
    {
        $saleRow = new \stdClass();
        $saleRow->id = 2002108179;
        $saleMultiList = (new Sale())::getAllSaleList(
            ['id', 'uri', 'status', 'paidTime', 'winUserinfoId'],
            ['pid' => $saleRow->id], '', null, null, true
        );
        // *********订单迁移二期2019.10.29*********

        $eq = DiffArray::transfer('tag_pc_sale_second_stage_pid', $saleMultiList, function () use ($saleRow) {
            $saleMultiList = Order::getOrderByPid($saleRow->id, [], ['saleId', 'winUserinfoId', 'snapshot', 'status', 'paidTime'], ['uri']);
            return $saleMultiList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_promotionLogic()
    {
        $saleId = 2002108179;
        $uid = 44830242;
        $saleList = Sale::getAllSaleList(['uri', 'winUserinfoId'], ['winUserinfoId' => $uid, 'pid' => $saleId, 'isDel' => 0], '', 1);
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage_pid', $saleList, function () use ($uid, $saleId) {
            $saleList = Order::getOrderByPid($saleId, ['winUserinfoId' => $uid], ['winUserinfoId', 'snapshot'], ['uri']);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    function test_standardGoodsLogic()
    {
        $standardGoodsRow = new \stdClass();
        $standardGoodsRow->id = 2002202137;
        $uid = 47823732;
        $columns = ['id', 'uri', 'status', 'isDel', 'winJson', 'profileJson', 'category', 'secCategory'];
        $where = [
            'winUserinfoId' => $uid,
            'goodsId' => $standardGoodsRow->id,
            'type' => 7,
            'status' => 'deal',
            'isDel' => 0,
        ];

        $saleList = Sale::getAllSaleList($columns, $where, 'id DESC', null, null, true);

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($uid, $standardGoodsRow) {
            $saleIdsList = Sale::getSaleBuyerOrderList("notPay", $uid, ['id']);
            $saleList = [];
            if ($saleIdsList) {
                $saleIds = array_pluck($saleIdsList, "id");
                $saleList = Order::getOrderAndSaleListById($saleIds, ['winJson', 'status',], ['id', 'uri', 'isDel', 'profileJson', 'category', 'secCategory'], ['status' => 'deal', 'draftId' => $standardGoodsRow->id, 'type' => 7, 'isDel' => 0]);
            }
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_OrderLogic()
    {
        $uid = 1;
        $columns = ['id', 'type', 'secCategory', 'category', 'userinfoId', 'priceJson', 'enableReturn', 'expressFee', 'endTime', 'createTime',
            'profileJson', 'uri', 'status', 'dispute', 'disputeTime', 'isRated', 'unsoldReason', 'winJson', 'winUserinfoId',
            'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime', 'launchTime', 'systemBzjJson'];

        $where = [
            'winUserinfoId' => $uid,
            'isDel' => 0,
            'status' => 'paid'

        ];

        /**
         * 替换为sale服务@190508
         * 两种方式取得数据最终返回给前端已做过一致性校验
         */
        $saleClient = new Sale();
        $saleClient::forceIndex('winUserinfoId');
        $_saleList = $saleClient::getAllSaleList($columns, $where, 'paidTime ASC');

        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $_saleList, function () use ($uid) {
            $where = [
                'winUserinfoId' => $uid,
                'status' => 2
            ];
            $orderColumns = ['saleId', 'saleType', 'userinfoId',
                'status', 'dispute', 'disputeTime', 'isRated', 'unsoldReason', 'winJson', 'winUserinfoId',
                'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime', 'launchTime'];
            $saleColumns = ['endTime', 'createTime', 'profileJson', 'uri', 'secCategory', 'category', 'enableReturn', 'expressFee', 'priceJson', 'systemBzjJson'];
            $_saleList = Order::getOrderListAttachSale($where, $orderColumns, $saleColumns, null, 0, 'paidTime ASC');
            return $_saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_AssetsLogic()
    {
        $uid = 1;
        $saleList = Sale::getAllSaleList(['status', 'winJson', 'paidTime'], [
            'userinfoId' => $uid,
            'isDel' => 0,
            "status IN('deal','refunding','deliveryReturn')" => null
        ]);
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $saleList, function () use ($uid) {
            $saleList = Order::getOrderList([
                'userinfoId' => $uid,
                "status" => [1, 5, 10]
            ], ['status', 'winJson', 'paidTime'], []);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_DepotOrderLogic()
    {
        $saleIds = [48, 49];
        $columns = ['id', 'userinfoId', 'type', 'dispute', 'profileJson', 'paidTime', 'winUserinfoId', 'winJson', 'uri', 'status', 'finishedTime', 'launchTime', 'deliveryTime', 'unsoldReason'];
        $where = ['id' => $saleIds];
        $_saleList = Sale::getAllSaleList($columns, $where);
        // *********订单迁移二期2019.10.29*********
        $eq = DiffArray::transfer('tag_pc_sale_second_stage', $_saleList, function () use ($saleIds) {
            $new_columns = ['saleId', 'userinfoId', 'saleType', 'dispute', 'paidTime', 'winUserinfoId', 'winJson', 'status', 'finishedTime', 'launchTime', 'deliveryTime', 'unsoldReason'];
            $_saleList = Order::getOrderAndSaleListById($saleIds, $new_columns, ['profileJson', 'uri']);
            return $_saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    function test_3_ReceiptTimeEndCommand_1()
    {
        /**
         * select * from pc.sale where status = 'agreeReturn' and launchTime < 1572682228 and dispute <= 1 and isDel = 0;
         * select * from sale_order.sale_order where status = 8 and launchTime < 1572682228 and dispute <= 1;
         */
        $nowTime = time();
        $whereSql = [
            'isDel' => 0,
            'status' => "agreeReturn",
            'launchTime <' => ($nowTime - 7 * 86400),
            'dispute <=' => 1
        ];
        $columns = ['id', 'uri', 'category', 'secCategory', 'type', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime',
            'recommendTime', 'deliveryTime', 'endTime', 'createTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson',
            'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn', 'dispute'];

        $saleList = Sale::getAllSaleList($columns, $whereSql, 'id ASC', 1000);
        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage_preview', $saleList, function () use ($nowTime) {
            $condition = [
                'status' => OrderStatus::AGREE_RETURN,
                'launchTime <' => ($nowTime - 7 * 86400),
                'dispute <=' => 1
            ];
            $orderFields = ['saleId', 'snapshot', 'saleType', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime', 'deliveryTime', 'endTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson', 'dispute'];
            $saleFields = ['uri', 'category', 'secCategory', 'recommendTime', 'createTime', 'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, 1000, null, 'saleId asc', 'idx_status_launchTime');
            return $saleList;
        }, __METHOD__, ['profile.activityCode', 'profile.fee', 'profile.isLiveOrder', 'profile.liveOrderCharge', 'profile.depotId']);
        $this->assertEquals(true, $eq);
    }

    function test_3_ReceiptTimeEndCommand_2()
    {
        /**
         * select * from pc.sale where status = 'delivery' and delayReceiptTime > 0 and delayReceiptTime < 1573287028 and dispute <= 1 and isDel = 0;
         * select * from sale_order.sale_order where status = 3 and delayReceiptTime > 1570608628 and delayReceiptTime < 1573287028 and dispute <= 1;
         */
        $nowTime = time();
        $columns = ['id', 'uri', 'category', 'secCategory', 'type', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime',
            'recommendTime', 'deliveryTime', 'endTime', 'createTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson',
            'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn', 'dispute'];

        //发货状态，到收货时间自动确认收货
        $whereSql = [
            'isDel' => 0,
            'status' => "delivery",
            'delayReceiptTime >' => 0,
            'delayReceiptTime <' => $nowTime,
            'dispute <=' => 1
        ];
        Sale::forceIndex('receiptTimeEnd');
        $saleList = Sale::getAllSaleList($columns, $whereSql, 'id ASC', 1000);
        //重置索引
        Sale::forceIndex('');

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($nowTime) {
            $condition = [
                'status' => OrderStatus::DELIVERY,
                'delayReceiptTime >' => $nowTime - 31 * 86400,
                'delayReceiptTime <' => $nowTime,
                'dispute <=' => 1
            ];
            $orderFields = ['saleId', 'snapshot', 'saleType', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime', 'deliveryTime', 'endTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson', 'dispute'];
            $saleFields = ['uri', 'category', 'secCategory', 'recommendTime', 'createTime', 'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, 1000, null, 'saleId asc', 'idx_status_delayReceiptTime_dispute_saleId');
            return $saleList;
        }, __METHOD__, ['profile.activityCode', 'profile.fee', 'profile.isLiveOrder', 'profile.liveOrderCharge', 'profile.depotId']);
        $this->assertEquals(true, $eq);
    }

    function test_3_ReturnTimeEndCommand()
    {
        $nowTime = time();
        $whereSql = [
            'isDel' => 0,
            'status' => 'returning',
            'launchTime <' => $nowTime - 2 * 86400,
            'dispute <=' => 1
        ];

        $columns = ['id', 'uri', 'category', 'secCategory', 'type', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime',
            'recommendTime', 'deliveryTime', 'endTime', 'createTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson',
            'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn', 'dispute'];

        $saleList = Sale::getAllSaleList($columns, $whereSql, 'launchTime ASC');

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($nowTime) {
            $condition = [
                'status' => OrderStatus::RETURNING,
                'launchTime <' => $nowTime - 2 * 86400,
                'dispute <=' => 1
            ];
            $orderFields = ['saleId', 'snapshot', 'saleType', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime', 'deliveryTime', 'endTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson', 'dispute'];
            $saleFields = ['uri', 'category', 'secCategory', 'recommendTime', 'createTime', 'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, null, null, 'launchTime asc');
            return $saleList;
        }, __METHOD__, ['profile.depotId', 'profile.depotUserId', 'profile.pdId', 'profile.depotPdId']);
        $this->assertEquals(true, $eq);
    }

    function test_3_RefundTimeEndCommand()
    {
        /**
         * select * from pc.sale where isDel = 0 and dispute <= 1 and ((status IN ("refunding") AND launchTime < 1573960659) OR (status IN("deliveryReturn") AND launchTime < 1573528659))
         * =>
         * select * from sale_order.sale_order where launchTime > 1572923859 and launchTime < 1573960659 and dispute <= 1 and status = 5;
         * select * from sale_order.sale_order where launchTime > 1572923859 and launchTime < 1573528659 and dispute <= 1 and status = 10;
         *
         */
        $nowTime = time();
        $whereSql = [
            'isDel' => 0,
            '((status IN ("refunding") AND launchTime < ' . ($nowTime - 2 * 86400) . ') OR (status IN("deliveryReturn") AND launchTime < ' . ($nowTime - 7 * 86400) . '))' => null,
            'dispute <=' => 1
        ];

        $columns = ['id', 'uri', 'category', 'secCategory', 'type', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime',
            'recommendTime', 'deliveryTime', 'endTime', 'createTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson',
            'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn', 'dispute'];

        $saleList = Sale::getAllSaleList($columns, $whereSql, 'launchTime ASC');

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($nowTime) {
            $orderFields = ['saleId', 'saleType', 'isRated', 'paidTime', 'finishedTime', 'delayPayTime', 'deliveryTime', 'endTime', 'status', 'userinfoId', 'winUserinfoId', 'winJson', 'dispute'];
            $saleFields = ['uri', 'id', 'category', 'secCategory', 'recommendTime', 'createTime', 'multiWins', 'handicraft', 'profileJson', 'priceJson', 'enableReturn'];
            $refundingCondition = [
                'launchTime > ' . ($nowTime - 14 * 86400) => null,
                'launchTime < ' . ($nowTime - 2 * 86400) => null,
                'dispute <=' => 1,
                'status' => OrderStatus::REFUNDING
            ];
            $DeliveryReturnCondition = [
                'launchTime > ' . ($nowTime - 14 * 86400) => null,
                'launchTime < ' . ($nowTime - 7 * 86400) => null,
                'dispute <=' => 1,
                'status' => OrderStatus::DELIVERY_RETURN
            ];
            $refundingSaleList = Order::getOrderListAttachSale($refundingCondition, $orderFields, $saleFields, null, null, 'launchTime asc');
            $deliveryReturnSaleList = Order::getOrderListAttachSale($DeliveryReturnCondition, $orderFields, $saleFields, null, null, 'launchTime asc');
            $saleList = array_merge($refundingSaleList ?? [], $deliveryReturnSaleList ?? []);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_3_QuickSendLogic()
    {
        $whereIn = '';
        $userinfoId = 1;
        $whereInSql = empty($whereIn) ? ['paid'] : $whereIn;
        $saleList = Sale::getAllSaleList(['id', 'profileJson', 'uri', 'status'], ['userinfoId' => $userinfoId, 'isDel' => 0, 'status' => $whereInSql]);
        // *********订单迁移三期2019.11.06*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($userinfoId) {
            $saleFields = ['id', 'uri', 'profileJson'];
            $orderFields = ['status', 'saleId'];
            $saleList = Order::getOrderListAttachSale(['userinfoId' => $userinfoId, 'status' => OrderStatus::PAID], $orderFields, $saleFields);
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_3_LiveSaleLogin()
    {
        $uid = 977745;
        $goodsRow = new \stdClass();
        $goodsRow->id = 4095538;
        $type = SaleConst::SALE_TYPE['live'];
        $goodsId = $goodsRow->id;
        $columns = ['id', 'uri', 'status', 'isDel', 'winJson', 'profileJson'];
        $where = [
            'winUserinfoId' => $uid,
            'goodsId' => $goodsRow->id,
            'type' => SaleConst::SALE_TYPE['live'],
            'status' => 'deal',
            'isDel' => 0,
        ];

        $saleList = Sale::getAllSaleList($columns, $where, 'id DESC', null, null, true);

        // *********订单迁移三期2019.11.06*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($uid, $type, $goodsId) {
            $saleIdsList = Sale::getSaleBuyerOrderList("notPay", $uid, ['id']);
            $saleList = [];
            if ($saleIdsList) {
                $saleIds = array_pluck($saleIdsList, "id");
                $saleList = Order::getOrderAndSaleListById($saleIds, ['winJson', 'status',], ['id', 'uri', 'isDel', 'profileJson'], ['status' => 'deal', 'draftId' => $goodsId, 'type' => $type, 'isDel' => 0]);
            }
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    function test_3_SyncCloseLiveNoStockSale()
    {
        $winUserinfoId = 1;
        $goodId = 4095538;
        $type = 0;
        $where = [
            'goodsId' => $goodId,
            'type' => $type,
            'isDel' => 0,
            'winUserinfoId <>' => $winUserinfoId,
            'status' => 'deal'
        ];

        $saleIds = Sale::getAllSaleList(['id'], $where, '', null, null, true);

        // *********订单迁移三期2019.11.06*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleIds, function () use ($winUserinfoId, $goodId, $type) {
            $saleIdsList = Order::getOrderList(["winUserinfoId <>" => $winUserinfoId, 'saleType' => $type, 'status' => OrderStatus::DEAL], ['saleId'], [], null, null, '', 'idx_status_launchTime');
            $saleIds = [];
            if ($saleIdsList) {
                $ids = array_pluck($saleIdsList, "id");
                $saleIds = \SaleService\Modules\Sale::getSaleList($ids, ['id'], ['draftId' => $goodId, 'isDel' => 0]);
            }
            return $saleIds;
        }, __METHOD__);

        $this->assertEquals(true, $eq);
    }

    function test_3_deliceryTimeEndCommand()
    {
        $saleFields = [
            'id', 'category', 'secCategory', 'type', 'uri', 'userinfoId', 'goodsId', 'category', 'priceJson', 'multiWins',
            'openTime', 'likes', 'views', 'createTime', 'endTime', 'enableReturn', 'expressFee', 'finishedTime', 'paidTime',
            'recommendTime', 'isDel', 'isShow', 'profileJson', 'status', 'winUserinfoId', 'winJson', 'handicraft', 'pid', 'dispute'
        ];
        $maxId = 0;
        $nowTime = time();
        $whereSql = [
            'isDel' => 0,
            'status' => 'paid',
            'paidTime >' => 0,
            'paidTime <' => ($nowTime - 31 * 86400),
            'dispute <=' => 1,
            'type !=' => 5,
            'id >' => $maxId
        ];
        $saleList = Sale::getAllSaleList($saleFields, $whereSql, 'paidTime ASC', 1000);
        $fields = [
            'userinfo' => ['sellerLevelScores'],
            'userinfo_verify' => ['expiredTime']
        ];
        $userinfoIds = array_unique(array_column($saleList, 'userinfoId'));
        $userinfoList = Userinfo::batchGetUserinfoSnsByField($userinfoIds, $fields);

        //未按约定时间发货
        $reasonId = 2;
        $reasonList = OrderConst::BUYER_REASON_IDS;
        $reason = $reasonList['refund'][$reasonId];
        $newList = [];
        foreach ($saleList as $sale) {
            $sale->userinfo = $userinfoList[$sale->userinfoId] ?? new \stdClass();
            //积分
            $sellerLevelScores = get_property($sale->userinfo, 'sellerLevelScores', 0);
            $sellerLevel = CommonUtil::sellerLevel($sellerLevelScores);
            $expiredTime = get_property($sale->userinfo, 'expiredTime', 0);

            if ($sellerLevel < 2 && $expiredTime < time()) {
                //1级未认证商家不予自动退款
                continue;
            }
            $newList[] = $sale;

        }
        $saleList = $newList;
        // *********订单迁移三期2019.11.06*********

        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($nowTime, $maxId) {
            $condition['paidTime >'] = ($nowTime - 60 * 86400);
            $condition['paidTime <'] = ($nowTime - 31 * 86400);
            $condition['status'] = OrderStatus::PAID;
            $condition['saleType !='] = 5;
            $condition['saleId >'] = $maxId;
            $condition['dispute <='] = 1;

            $orderFields = ['saleId', 'snapshot', 'saleType', 'userinfoId', 'finishedTime', 'paidTime', 'status', 'winUserinfoId', 'winJson', 'dispute'];

            $saleFields = ['category', 'secCategory', 'uri', 'goodsId', 'priceJson', 'endTime', 'multiWins', 'openTime', 'likes', 'views', 'createTime', 'enableReturn', 'expressFee', 'recommendTime', 'isDel', 'isShow', 'profileJson', 'pid'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, 1000, null, "paidTime ASC", "idx_status_launchTime");
            $fields = [
                'userinfo' => ['sellerLevelScores'],
                'userinfo_verify' => ['expiredTime']
            ];
            $userinfoIds = array_unique(array_column($saleList, 'userinfoId'));
            $userinfoList = Userinfo::batchGetUserinfoSnsByField($userinfoIds, $fields);

            //未按约定时间发货
            $reasonId = 2;
            $reasonList = OrderConst::BUYER_REASON_IDS;
            $reason = $reasonList['refund'][$reasonId];
            $newList = [];
            foreach ($saleList as $sale) {
                $sale->userinfo = $userinfoList[$sale->userinfoId] ?? new \stdClass();
                //积分
                $sellerLevelScores = get_property($sale->userinfo, 'sellerLevelScores', 0);
                $sellerLevel = CommonUtil::sellerLevel($sellerLevelScores);
                $expiredTime = get_property($sale->userinfo, 'expiredTime', 0);

                if ($sellerLevel < 2 && $expiredTime < time()) {
                    //1级未认证商家不予自动退款
                    continue;
                }
                $newList[] = $sale;

            }
            $saleList = $newList;
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_3_PayTimeEndCommand()
    {
        $columns = ['id', 'isDel', 'status', 'type', 'delayPayTime'];
        $lastSaleId = 0;
        $limit = 200;
        $nowTime = time();
        $startTime = time();
        $whereSql = [
            'type' => self::PAY_TIME_END_SALE_TYPE,
            'isDel' => 0,
            'status' => 'deal',
            'delayPayTime >' => $startTime - (30 * 86400),
            'delayPayTime <=' => $nowTime - 1800
        ];
        $whereSql['id >'] = $lastSaleId;
        $saleList = Sale::getAllSaleList($columns, $whereSql, 'id ASC', $limit, null, false);
        // *********订单迁移三期2019.11.06*********

        $type = self::PAY_TIME_END_SALE_TYPE;
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($whereSql, $lastSaleId, $type, $limit) {
            $condition['status'] = OrderStatus::DEAL;
            $condition['saleType'] = $type;
            $condition['saleId >'] = $lastSaleId;
            $condition['delayPayTime >'] = $whereSql['delayPayTime >'];
            $condition['delayPayTime <='] = $whereSql['delayPayTime <='];

            $orderFields = ['saleId', 'delayPayTime', 'saleType', 'status'];
            $saleFields = ['id', 'isDel'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, $limit, null, "saleId ASC", 'idx_status_launchTime');
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_3_rateTimeEndCommand()
    {
        $columns = ['id'];
        $nowTime = time();
        $whereSql = [
            "isRated" => 0,
            "isDel" => 0,
            "status" => "finished",
            "finishedTime <" => time() - 7 * 86400,
            "finishedTime >" => time() - 35 * 86400
        ];
        $saleClient = new Sale();
        $saleClient::forceIndex('rateTimeEnd');
        $saleList = $saleClient::getAllSaleList($columns, $whereSql, 'id ASC', 3000);
        // *********订单迁移三期2019.11.06*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage_preview', $saleList, function () use ($nowTime) {
            $orderFields = ['saleId'];
            $condition['isRated'] = 0;
            $condition['status'] = OrderStatus::FINISHED;
            $condition['finishedTime <'] = $nowTime - 7 * 86400;
            $condition['finishedTime >'] = $nowTime - 35 * 86400;
            $saleList = Order::getOrderList($condition, $orderFields, [], 3000, null, "saleId ASC", 'idx_status_isRated_finishedTime');
            return $saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_3_DeductSellerScoreCommand()
    {
        $endTime = mktime(0, 0, 0);
        $startTime = $endTime - 86400;
        $limitTime = 8 * 86400;
        //发货超过8天，并且已确认收货，如果是包退拍品保证是第一次确认收货
        $whereSql = [
            'type' => 0,
            'enableReturn <=1' => null,
            'status' => 'finished',
            'paidTime >0' => null,  //排除当面交易拍品
            'deliveryTime >paidTime' => null,//发货时间早于付款时间SQL会报错，测试数据会出现这种情况
            'deliveryTime - paidTime >' . $limitTime => null,
            'finishedTime >=' . $startTime => null,
            'finishedTime <' . $endTime => null,
            'isDel' => 0
        ];
        $saleList = Sale::getAllSaleList(['id', 'uri', 'userinfoId', 'winJson', 'profileJson', 'deliveryTime', 'paidTime'], $whereSql, '', 1500);
        // *********订单迁移三期2019.11.06*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $saleList, function () use ($limitTime, $startTime, $endTime) {

            $condition['status'] = OrderStatus::FINISHED;
            $condition['saleType'] = 0;
            $condition['paidTime >'] = time() - 30 * 86400;
            $condition['finishedTime <'] = $endTime;
            $condition['finishedTime >='] = $startTime;
            $condition['deliveryTime - paidTime >'] = $limitTime;

            $orderFields = ['saleId', 'userinfoId', 'winJson', 'deliveryTime', 'paidTime'];
            $saleFields = ['id', 'uri', 'profileJson', 'enableReturn'];
            $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, 1500, null, "saleId ASC");
            $saleList = collect($saleList)->filter(function ($item) {
                return $item->enableReturn <= 1;
            });
            return $saleList->toArray();
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_singleGetSale()
    {
        $result = Sale::singleGetSale(1975358182, null);
        $this->assertNotEmpty($result);
    }

    function test_batchGetSale()
    {
        $ids = [1975358182, 1975358176];
        $fields = null;
        $filter = [];
        $result = Sale::batchGetSale($ids, $fields, $filter);
        $this->assertNotEmpty($result);
    }

    function test_getSaleList()
    {
        $ids = [1975358182, 1975358176];
        $fields = null;
        $filter = [];
        $result = Sale::getSaleList($ids, $fields, $filter);
        $this->assertNotEmpty($result);
    }

    function test_DepositController_process()
    {
        $saleId = 1975358182;
        $sale = Sale::getAllSaleList(SaleConst::ALL_COLUMNS, ['id' => $saleId], '', null, null, true);
        $sale = $sale[0] ?? [];

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $sale, function () use ($saleId) {
            $orderFields = ['saleId', 'saleType', 'userinfoId', 'endTime', 'status', 'dispute', 'disputeTime', 'isRated', 'unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime', 'launchTime'];
            $saleFields = ['goodsId', 'category', 'secCategory', 'handicraft', 'priceJson', 'enableReturn', 'expressFee', 'multiWins', 'openTime', 'createTime', 'isDel', 'isShow', 'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderAndSaleById($saleId, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__, ['profile.activityCode', 'profile.preSell']);
        $this->assertEquals(true, $eq);
    }

    function test_refundBzjSellerAction()
    {
        $uri = '1804081458tzbp85';
        $sale = Sale::getAllSaleList(SaleConst::ALL_COLUMNS, ['uri' => $uri]);
        $sale = $sale[0] ?? [];

        // *********订单迁移三期 @hhf*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $sale, function () use ($uri) {
            $orderFields = ['saleId', 'saleType', 'userinfoId', 'endTime', 'status', 'dispute', 'disputeTime', 'isRated', 'unsoldReason', 'winJson', 'winUserinfoId', 'delayPayTime', 'delayReceiptTime', 'paidTime', 'deliveryTime', 'finishedTime', 'launchTime'];
            $saleFields = ['goodsId', 'category', 'secCategory', 'handicraft', 'priceJson', 'enableReturn', 'expressFee', 'multiWins', 'openTime', 'createTime', 'isDel', 'isShow', 'profileJson', 'uri', 'recommendTime', 'likes', 'views', 'isShare', 'systemBzjJson', 'pid'];
            $sale = Order::getOrderAndSaleById($uri, $orderFields, $saleFields);
            return $sale;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }


    function test_getSaleByPid()
    {
        $saleRow = new \stdClass();
        $saleRow->id = 2002108179;
        $_saleList = Sale::getSaleByPid($saleRow->id, ['winUserinfoId'], true);
        // *********订单迁移三期2019.11.15*********
        $eq = DiffArray::transfer('tag_pc_sale_tertiary_stage', $_saleList, function () use ($saleRow) {
            $_saleList = Order::getOrderByPid($saleRow->id, [], ['winUserinfoId']);
            return $_saleList;
        }, __METHOD__);
        $this->assertEquals(true, $eq);
    }

    function test_batchGetSaleWithShopInfo()
    {
        $ids = [1975358182, 1975358176];
        $fields = null;
        $result = Sale::batchGetSaleWithShopInfo($ids, ['uri', 'userinfoId'], []);
        $this->assertNotEmpty($result);
    }
}