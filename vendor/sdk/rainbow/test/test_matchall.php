<?php


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


$result = check_keywords("亲,这里不方便讲话，需要了解加QQ或者加我vx聊", "sensitive-word", array("blacklist", "blacklist_search"));

var_dump($result);