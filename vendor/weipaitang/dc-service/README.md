# data-center 数据服务层

## 0. 安装
```bash
composer require weipaitang/dc-service:~0.0.1
```


## 1. 运行

```bash
composer install 

cd tests/

1 /xxx/xxx/php /xx/xx/phpunit --no-configuration --filter "/(::testReport)( .*)?$/" Tests\BaseTest /xxx/xx/dc-service/tests/BaseTest.php
2 直接phpstorm 单元测试


```


## 2. 报表数据 && 必须try..catch
```php
<?php

use WptDataCenter\Handler\CurlHandler;
use WptDataCenter\Report\UserReport;

// 最全设置选项
CurlHandler::getInstance()
    ->setRetries(2)// 设置重试次数
    ->setTimeout(2)// 设置超时时间 单位 秒
    ->setConnectTimeout(2)// 设置连接时间 单位 秒
    ->setEndpoint("http://www.wpt.com")// 设置服务端点
    ->setTraceOpts(2)// 设置报错回溯信息内容
    ->setTraceLimit(10)// 设置报错回溯信息层级
    ->go("/dc/list", ["uid" => 2, "fields" => ["last_15d_refund_num"]]);

// 基础请求 (默认 超时2s 连接2秒 不重试 回溯无参数 回溯层级5 端点 env(MICRO_GATEWAY))
CurlHandler::getInstance()->go("/dc/list", ["uid" => 2, "fields" => ["last_15d_refund_num"]]);


// 必须 **使用** try{} catch()...  如果有异常后 是记录日志 还是阻断程序执行 根据具体业务决定
try {
    UserReport::getInstance()->get(2, ['last_15d_refund_num']);    
} catch (Throwable $e) {
    echo $e->getMessage();
    // log::info(xxx);
    // return xxx;
}
```

## 3.订阅数据获取 && 必须try..catch
```php
<?php

use WptDataCenter\Event\Subscribe;

try {
    $result = Subscribe::getInstance()->get(25382764, 'group_buy_price');
} catch (\Throwable $e) {
    echo $e->getMessage();
}


```

