# 微拍堂订单 sdk (开发中)

第一期是建立在 saleGo 基础上进行开发，暂时只对 api 项目提供支持。

## 使用方法

### 在 lumen 框架中使用

#### 注册服务提供者

在 bootstrap 文件夹下的 app.php 文件中添加以下代码
```php
$app->register(\WptOrder\OrderService\OrderServiceProvider::class);
```
#### 配置文件

1、 在 `config` 文件夹下加入 `order.php` 文件

2、 在 `order.php` 中覆盖默认配置，举例如下：

```php
return [
    'api' => 'saleGo',
    'log' => [
        'type' => 'daily', // 日志类型：daily，hourly
        'file' => storage_path() . "/logs/order-service.log",// 日志文件名
    ]
];
```

3、 在 bootstrap 文件夹值下的 `app.php` 文件中添加以下代码：
```php
$app->configure('order');
```

#### 调用示例

```php
use WptOrder\OrderService\Facades\Order;

// 根据 id 获取订单信息
Order::getOrderById(19865382947, ['userinfoId', 'winUserinfoId', 'status']);

// 根据拍品 uri 获取订单信息
Order::getOrderByUri(19865382947, ['userinfoId', 'winUserinfoId', 'status']);

```

### 在其他项目中使用

#### 简单调用

```php
OrderService::getInstance()->getOrderById(1,  ['userinfoId', 'winUserinfoId', 'status']);
```

#### 自定义配置信息

```php
$config =  [
              'api' => 'saleGo',
              'log' => [
                  'type' => 'daily', // 日志类型：daily，hourly
                  'file' => storage_path() . "/logs/order-service.log",// 日志文件名
              ]
          ];
          
OrderService::getInstance($config)->getOrderById(1,  ['userinfoId', 'winUserinfoId', 'status']);

```

# 配置说明

目前所有配置项如下：
```php
[
    'api' => 'saleGo', // 指定数据源 api 配置，目前只支持 saleGo
    'log' => [// 日志配置
        'type' => 'daily', // 日志类型：daily，hourly
        'file' => __DIR__ . "/../logs/order-service.log",// 日志文件名
    ]
]
```


# 帮助函数

帮助函数见 helpers.php 文件