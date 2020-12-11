### 安装

```shell
composer config repositories.proprietary composer https://packagist.wpt.la
composer require pay/php-sdk "1.*"
```



### 配置

在 .env 或者环境变量中加入（正式环境配置参数请联系支付组同事）

```ini
PAYCENTER_HOST=http://payapit.weipaitang.com
PAYCENTER_PRODUCT=1
PAYCENTER_KEY=weipaitang
```



### 使用
[支付接口文档](http://confluence2.weipaitang.com/pages/viewpage.action?pageId=1703949)

#### 请求

```php
use PayCenter\Request\Pay\UnifiedOrder\RechargeRequest;
use PayCenter\Exception\{ApiException, RequestException, ResponseException};

try {
    $req = new RechargeRequest();
    //设置接口参数
    $req->setMoney(100);
    //请求接口
    $res = $req->request(); /*或者简化调用*/ $req();
    //使用返回结果
    $res->getOrderNo();

    //返回的 Response 对象始终是有效可用的，无需额外判断
    //请求失败或者接口出错时会抛出异常，建议按需捕获
} catch (ApiException $e) {
    //接口逻辑处理失败（明确返回的失败）
    //错误码：$e->getCode()
    //错误信息：$e->getMessage()
} catch (RequestException $e) {
    //接口请求失败（cURL 请求失败、超时）
    //请求参数：$e->request
    //请求地址：$e->url
    //建议记录日志
} catch (ResponseException $e) {
    //接口返回内容解析失败、签名错误等
    //接口请求参数：$e->response->request
    //原始返回结果：$e->response->original;
    //建议记录日志
} catch (\Throwable $th) {
    //其他异常：配置参数错误、其他业务代码出错等
}
```

#### 回调

```php
use PayCenter\Notify\PayNotify;
use PayCenter\Exception\ResponseException;

try {
    //直接传入原始回调请求字符串（HTTP Raw Body）
    //laravel: $content = app('request')->getContent();
    //plain: $content = file_get_contents('php://input');
    $notify = new PayNotify($content);

    //该对象会自动校验内容是否合法，签名是否正确。如果不正确会抛出异常

    //使用回调数据
    $notify->getOutTradeNo();

    //处理完成后需输出文本：SUCCESS
} catch (ResponseException $e) {
    //回调数据校验失败
    //需记录日志
} catch (\Throwable $th) {
    //其他代码异常
}
```


### 更新

```shell
composer update pay/php-sdk
```



### 反馈

若有任何建议或者问题，欢迎给我们 [提 issue](https://gitlab.weipaitang.com/pay/php-sdk/issues/new)