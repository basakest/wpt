# 服务总线

<br/>

## 调用方使用

### 1. composer包引入

```$xslt
composer require weipaitang/bus:~0.0.1
```

### 2. 在 lumen/laravel 框架中使用

#### 注册服务提供者

在 bootstrap 文件夹下的 app.php 文件中添加以下代码
```php
$app->register(\WptBus\BusProvider::class);

$app->configure('bus');
```
#### 配置文件

1、 在 `config` 文件夹下加入 `bus.php` 文件

2、 在 `bus.php` 中覆盖默认配置（默认只要填写远程地址，更多配置查看SDK中Config目录），举例如下（推荐使用env）：

```php
return [
    'order' => [ // 服务名
        'http' => [ // http配置
             'servers' => [ // 服务地址，默认一个，有主备切换需求可以配置多个
                  env('MICRO_GATEWAY')
             ]
        ]
    ]
];
```
3、在.env添加MICRO_GATEWAY地址
```bash
# 测试指定环境
MICRO_GATEWAY=http://10.3.7.34:8080/

# 测试tke多环境
MICRO_GATEWAY=http://test-micro-gw.wptqc.com/

# 灰度环境 指定host 10.3.1.223 micro-gw-vpc.wptqc.com
MICRO_GATEWAY=http://micro-gw-vpc.wptqc.com/

# 线上环境
MICRO_GATEWAY=http://micro-gw-vpc.wptqc.com/
```

#### 调用示例
```php
use WptBus\Facades\Bus;

$result = Bus::order()->test($param);

```

### 3. 其它项目使用（建议将Bus封装成单例模式）
```php

use \WptBus\Bus;

$config = [
    'order' => [ // 服务名
        'http' => [ // http配置
            'servers' => [
                'http://10.3.7.2:8080',
                //'http://172.16.24.114:8080',
            ]
        ]
    ]
];
$bus = new Bus($config);

$result = $bus->order()->test($param);

```

### 4. 项目设置http配置和头部信息
```php

// 同时设置http配置和头部信息已经重试
$config = ["readTimeout" => 3000, "connectTimeout" => 1000];
$header = ["unique_id" => "my uniqueId"];
$retry = 1;
$result = Bus::user()->base->setHttpConfig($config, $header, $retry)->login(1, []);

// 设置uniqueId
$result = Bus::user()->base->setUniqueId("my uniqueId")->login(1, []);

// 设置超时
$result = Bus::user()->base->setTimeout(3000, 1000)->login(1, []);

// 设置重试
$result = Bus::user()->base->setRetryTimes(1)->login(1, []);


```

<br/>

******

## 服务方使用

### 1. 生成服务项目
```$xslt
php build -s test
```

### 2. 在对应项目的Application类编写请求
```php

class Application extends BaseService
{
    public function test($param)
    {
        // 参数验证
        $error = $this->validate($params, ['userinfoId' => 'required']);
        if ($error) {
            return $error;
        }
        
        // 请求服务
        $ret = $this->httpPost('route/test', $params);
        
        // 处理结果
        $this->dealResultData($ret, function ($data) {
            $data['newField'] = 'value';
            return $data;
        });

        // 自定义日志
        Log::error($this->serviceName, "请求记录", ['ret' => $ret]);
        
        return $ret;
    }
}
```

### 3. 生成的对应配置如下
```php
return [
    'http' => [
        'name' => 'order',
        'servers' => [],
        'balance' => 'mainSpare', // 主备
        'connectTimeout' => 2000, // 连接超时ms
        'readTimeout' => 2000, // 读超时ms
        'debug' => true, // 请求日志记录返回结果
        'token' => "", // token
        'checkSign' => true, // 检查签名
        'signKey' => "wpt", // 签名key
    ]
];
```

### 4. 查看日志目录
- http日志：micro-sdk/transport-http-{serviceName}
- SDK内自定义日志：micro-sdk/bus-sdk-{serviceName}

### 5. 意见
- 若服务内模块较多，将application类作为服务入口，根据属性生成模块对应的类，具体参照订单服务