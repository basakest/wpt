# 微拍堂通用工具包

## 使用方法

### 在 lumen 框架中使用

#### composer包引入

```$xslt
composer require weipaitang/common-library:~0.0.1
```

#### 注册服务提供者

在 bootstrap 文件夹下的 app.php 文件中添加以下代码
```php
$app->register(\WptCommon\Library\CommonLibraryProvider::class);

$app->configure('common-library');
```
#### 配置文件

1、 在 `config` 文件夹下加入 `common-library.php` 文件

2、 在 `order.php` 中覆盖默认配置，举例如下：

```php
return [
    'mlogger' => [
           /**
            * 日志级别
            */
            'log_level' => 'info',

            /**
             * 日志文件目录
             */
            'logs_dir' => 'newlogs',
    
            /**
             * 展开字段
             */
            'expand_fields' => ['id', 'saleid', 'uri', 'saleuri', 'createtime', 'endtime', 'opentime', 'type',
                'status', 'category', 'seccategory', 'price', 'userinfoid', 'userinfouri', 'winuserinfoid',
                'bail', 'balance', 'money', 'info', 'result', 'origin', 'code', 'errorcode', 'roomid', 'roomuri',
                'sc', 'orderno', 'outtradeno', 'totalfee', 'from', 'fromuri', 'fromid', 'number', 'uip', 'date',
                'time', 'tel', 'extend1', 'extend2', 'extend3']
        ]
    ]
];
```

#### 调用示例

##### laravel或lumen环境使用
```php
use WptCommon\Library\Facades\MLogger;

MLogger::info('xxfilename', 'this is a message', ['k' => 'v']);

```

#### 非laravel或lumen环境使用
```php
use WptCommon\Library\Tools\Logger;

Logger::getInstance()->info('xxfilename', 'this is a message', ['k' => 'v']);

```
