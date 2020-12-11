# RainbowSDK

## 简介

RainbowSDK是服务代理Rainbow的php客户端，提供一种更快捷轻量的接入服务代理Rainbow的方式。目前支持获取niffler热配置，关键词检测

## 调用示例

### 获取niffler热配置

```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

// 获取配置
function get_config($projectName, $key)
{
	try {
		$rainbow = Rainbow::getInstance();
		$ret = $rainbow->getConfig($projectName, $key);
		if ($ret->success) {
			if ($ret->type == Rainbow::FROM_FILE) {
				//TODO print log
			  //var_dump($ret->message);
			}

			return $ret->data;
		}
	} catch (\Exception $e) {
		//TODO print log
		var_dump($e->getMessage());
		return "";
	}
}


$result = get_config("wmapi.weipaitang.com", "hello");

var_dump($result);
```

### 检测文本是否有敏感词

```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

function check_keyword($text, $project, $keys) {
	try{
        $rainbow = Rainbow::getInstance();
        $ret = $rainbow->matchOnce($text, $project, $keys);
        return $ret;
	} catch (\Exception $e) {
        //TODO print log
        var_dump($e->getMessage());
        return false;
	}
}


$result = check_keyword("亲,这里不方便讲话，需要了解加QQ或者加我vx聊", "sensitive-word", array("blacklist", "im"));

var_dump($result);
```
输出
```
array(1) {
  [0]=>
  string(18) "这里不方便讲"
}
```

### 检测文本中所有可能的敏感词

```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

// 检测文本中可能包含的所有敏感词
function check_keywords($text, $project, $keys) {
	try{
		$rainbow = Rainbow::getInstance();
		$ret = $rainbow->matchAll($text, $project, $keys);
		return $ret;
	} catch (\Exception $e) {
		var_dump($e->getMessage());
		return false;
	}
}


$result = check_keywords("亲,这里不方便讲话，需要了解加QQ或者加我vx聊", "sensitive-word", array("blacklist", "im"));

var_dump($result);
```
输出
```
array(4) {
  [0]=>
  string(18) "这里不方便讲"
  [1]=>
  string(17) "需要了解加QQ"
  [2]=>
  string(8) "加我vx"
  [3]=>
  string(9) "习大大"
}
```

## 5种新增类型配置的读取
以下5种新增类型，如果指定的key不存在或者类型不匹配，会抛出异常

```
key 不存在时抛出：
[config-center,switchxx] error 100101, key not found
type 不匹配时抛出：
[config-center,switch] error 100101, type is not match
```

### 开关型配置

开关型配置返回布尔值 true/false,如果指定的key不存在或者类型不匹配，会抛出异常。
```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

try{
    $rainbow = Rainbow::getInstance();
    $result = $rainbow->getSwitchState("config-center", "switch");
    var_dump($result);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    return false;
}
```
输出
```
bool(true)
```

### 比率型配置

比率型配置返回float, 如果指定的key不存在或者类型不匹配，会抛出异常。
```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

try{
    $rainbow = Rainbow::getInstance();
    $result = $rainbow->getRation("config-center", "red-black");
    var_dump($result);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    return false;
}
```
输出
```
float(0.4)
```

### radio型配置

radio型配置返回string类型, 如果指定的key不存在或者类型不匹配，会抛出异常。
```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

try{
    $rainbow = Rainbow::getInstance();
    $result = $rainbow->getRadioOption("config-center", "live-push");
    var_dump($result);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    return false;
}
```
输出
```
string(5) "qiniu"
```

### checkbox型配置

checkbox型配置返回数组类型, 如果指定的key不存在或者类型不匹配，会抛出异常。
```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

try{
    $rainbow = Rainbow::getInstance();
    $result = $rainbow->getRadioOption("config-center", "push");
    var_dump($result);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    return false;
}
```
输出
```
array(2) {
  [0]=>
  string(4) "aaaa"
  [1]=>
  string(4) "cccc"
}
```

### 权重型配置

权重型配置返回string, 如果指定的key不存在或者类型不匹配，会抛出异常。
```php
require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

try{
    $rainbow = Rainbow::getInstance();
    $result = $rainbow->getEndpoint("config-center", "ip-pool");
    var_dump($result);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    return false;
}
```
输出
```
string(14) "10.3.7.72:3306"
```


