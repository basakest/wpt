<?php


require_once "./vendor/autoload.php";

use Rainbow\Rainbow;

// 检测是否有敏感词
function check_keyword($text, $project, $keys) {
	try{
		$rainbow = Rainbow::getInstance();
		$ret = $rainbow->matchOnce($text, $project, $keys);
		return $ret;
	} catch (\Exception $e) {
		var_dump($e->getMessage());
		return false;
	}
}


$result = check_keyword("亲,这里不方便讲话，需要了解加QQ或者加我vx聊", "sensitive-word", array("blacklist", "blacklist_search"));

var_dump($result);