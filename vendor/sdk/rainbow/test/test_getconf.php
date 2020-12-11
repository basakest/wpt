<?php


require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

// 获取配置
function getConfig($projectName, $key)
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


$config = getConfig("wmapi.weipaitang.com", "app-hot-update");

var_dump($config);