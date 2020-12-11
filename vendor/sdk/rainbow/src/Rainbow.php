<?php

namespace Rainbow;

/**
 * Class Rainbow
 * @package Rainbow
 */
class Rainbow
{

  const FROM_RAINBOW = 0;   //from rainbow
  const FROM_FILE = 1;      //from file
  const FROM_CACHE = 2;     //from cache

  const PROTO_V1 = 1;
  const FRAME_DATA = 1;

  const PLAIN_TEXT = 0;
  const ENCODE_JSON = 1;

  private static $_instance = null;

  private $sock_addr = "unix:///rainbow/niffler.sock";

  private $conf_path = "/niffler";

  private $client = null;

  private $cache = [];

  /**
   * Rainbow constructor.
   * @throws \Exception
   */
  private function __construct($isChroot)
  {
    if (PHP_SAPI == 'cli') {
      $this->sock_addr = 'unix:///data/www/rainbow/niffler.sock';
      $this->conf_path = '/data/www/niffler';
    }
    
    if (!$isChroot) {
      $this->sock_addr = 'unix:///data/www/rainbow/niffler.sock';
      $this->conf_path = '/data/www/niffler';
    }

    try {
      $this->client = fsockopen($this->sock_addr, -1, $errno, $errstr, 1);
    } catch (\Exception $e) {
      $this->client = null;
    }
  }

  /**
   * @return Rainbow|null
   */
  public static function getInstance($isChroot = TRUE)
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new Rainbow($isChroot);
    }

    return self::$_instance;
  }

  /**
   * @param string $project
   * @param string $key
   * @param int $timeout
   * @return \stdClass
   * @throws \Exception
   */
  public function getConfig($project, $key, $timeout = 300000)
  {
    try {
      return $this->_getConfigByType($project, $key, "text", $timeout);
    }catch (\Exception $e) {
      try {
        $filepath = sprintf($this->conf_path . '/%s/%s', $project, $key);
        $val = file_get_contents($filepath);

        return $this->_buildResp(true, self::FROM_FILE, $e->getMessage(), $val);
      } catch (\Exception $e) {
        if (!is_null($this->client)) {
          fclose($this->client);
        }

        $this->client = null;

        throw new \Exception(sprintf("read from file fail,msg:%s,project:%s,key:%s", $e->getMessage(),
          $project, $key));
      }
    }
  }

  private function _getConfigByType($project, $key, $type, $timeout = 300000)
  {
    $confkey = sprintf('%s/%s', $project, $key);
    if (isset($this->cache[$confkey]) && !empty($this->cache[$confkey])) {
      return $this->_buildResp(true, self::FROM_CACHE, "from cache", $this->cache[$confkey]);
    }

    $request["header"] = [
      "request_id" => $this->_getMillisecond(),
      "service" => "niffler",
    ];
    $request["api"] = "conf.getByType";
    $request["params"] = [
      "key" => $confkey,
      "type" => $type,
    ];


    if ($this->_send(json_encode($request), $timeout) === false) {
      fclose($this->client);
      $this->client = null;
      throw new \Exception("send error");
    }

    $resp = $this->_recv();
    if ($resp->code == 0) {
      return $this->_buildResp(true, self::FROM_RAINBOW, "from rainbow", $resp->data);
    }

    throw new \Exception(sprintf("[%s,%s] error %d, %s", $project, $key, $resp->code, $resp->data));
  }

  /**
   * 获取开关状态
   * @param string $project
   * @param string $keys
   * @param int $timeout 默认超时300ms
   * @return bool
   * @throws \Exception
   */
  public function getSwitchState($project, $key, $timeout = 300000)
  {
    $result = $this->_getConfigByType($project, $key, "switch", $timeout);
    if ($result->success) {
      return $result->data == "true" ? TRUE : FALSE;
    }
  }

  /**
   * radio单选配置
   * @param string $project
   * @param string $keys
   * @param int $timeout 默认超时300ms
   * @return string
   * @throws \Exception
   */
  public function getRadioOption($project, $key, $timeout = 300000)
  {
    $result = $this->_getConfigByType($project, $key, "radio", $timeout);
    if ($result->success) {
      return $result->data;
    }
  }

  /**
   * checkbox多选配置
   * @param string $project
   * @param string $keys
   * @param int $timeout 默认超时300ms
   * @return array
   * @throws \Exception
   */
  public function getCheckboxOption($project, $key, $timeout = 300000)
  {
    $result = $this->_getConfigByType($project, $key, "radio", $timeout);
    if ($result->success) {
      return json_decode($result->data);
    }
  }

  /**
   * 比率型配置
   * @param string $project
   * @param string $keys
   * @param int $timeout 默认超时300ms
   * @return float
   * @throws \Exception
   */
  public function getRation($project = '', $key = '', $timeout = 300000)
  {
    $result = $this->_getConfigByType($project, $key, "ration", $timeout);
    if ($result->success) {
      $nums = json_decode($result->data);
      return $nums[0]/$nums[1];
    }
  }

  /**
   * 负载均衡获取一个endpoint
   * @param string $project
   * @param string $keys
   * @return string
   * @throws \Exception
   */
  public function getEndpoint($project, $key, $timeout = 300000)
  {
    if ($key == "") {
      throw new \Exception("key is empty");
    }
    $confkey = sprintf('%s/%s', $project, $key);

    try {
      $request["header"] = [
        "request_id" => $this->_getMillisecond(),
        "service" => "niffler",
      ];
      $request["api"] = "balance.get";
      $request["params"] = $confkey;

      if ($this->_send(json_encode($request), $timeout) === false) {
        fclose($this->client);
        $this->client = null;
        throw new \Exception("send error");
      }

      $resp = $this->_recv();
      if ($resp->code == 0) {
        if ($resp->data) {
          return $resp->data;
        }
        return "";
      }
      throw new \Exception(sprintf("getEndpoint[%s,%s] error %d, %s", $project, $key, $resp->code, $resp->data));
    } catch (\Exception $e) {
      if (!is_null($this->client)) {
        fclose($this->client);
      }

      $this->client = null;

      throw new \Exception(sprintf("get endpoint fail,msg:%s,project:%s,key:%s", $e->getMessage(),
        $project, $key));
    }
  }

  /**
   * 完全匹配
   * @param string $text
   * @param string $project
   * @param array $keys
   * @return array
   * @throws \Exception
   */
  public function matchSame($text, $project, $keys, $timeout = 300000)
  {
    if ($text == "") {
      return [];
    }
    
    if (!is_array($keys)) {
      throw new \Exception("keys is not array"); 
    }

    return $this->_match("keyword.matchSame", $text, $project, $keys, $timeout);
  }

  /**
   * 检测文本中是否含有关键词
   * @param string $text
   * @param string $project
   * @param array $keys
   * @return array
   * @throws \Exception
   */
  public function matchOnce($text, $project, $keys, $timeout = 300000)
  {
    if ($text == "") {
      return [];
    }
    
    if (!is_array($keys)) {
      throw new \Exception("keys is not array"); 
    }

    return $this->_match("keyword.matchOnce", $text, $project, $keys, $timeout);
  }

  /**
   * 检测文本中所有可能的关键词
   * @param string $text
   * @param string $project
   * @param array $keys
   * @return array
   * @throws \Exception
   */
  public function matchAll($text, $project, $keys, $timeout = 300000)
  {
    if ($text == "") {
      return [];
    }
    if (!is_array($keys)) {
      throw new \Exception("keys is not array"); 
    }

    return $this->_match("keyword.matchWords", $text, $project, $keys, $timeout);
  }

  /**
   * @param $api
   * @param $text
   * @param $project
   * @param $key
   * @return array
   * @throws \Exception
   */
  private function _match($api, $text, $project, $keys, $timeout)
  {
    $request["header"] = [
      "request_id" => $this->_getMillisecond(),
      "service" => "niffler",
    ];
    $request["api"] = $api;
    $request["params"] = [
      "text" => $text,
      "project" => $project,
      "keys" => $keys,
    ];

    if ($this->_send(json_encode($request), $timeout) === false) {
      fclose($this->client);
      $this->client = null;
      return $this->localCheck($text, $project, $keys);
    }

    $resp = $this->_recv();
    if ($resp->code == 0) {
      if ($resp->data) {
        return $resp->data;
      }
      return [];
    }

    throw new \Exception(sprintf("matchOnce[%s,%s] error %d, %s", $project, json_encode($keys), $resp->code, $resp->data));
  }

  /**
   * @throws \Exception
   */
  private function _reconnect()
  {
    $this->client = fsockopen($this->sock_addr, -1, $errno, $errstr, 1);
    if (!$this->client) {
      throw new \Exception("fsockopen fail:$errstr($errno)");
    }
  }

  /**
   * @param $data
   * @return false|int
   * @throws \Exception
   */
  private function _send($data, $timeout)
  {
    if (is_null($this->client) || feof($this->client)) {
      $this->_reconnect();
    }

    $len = strlen($data);
    $header = pack("C5N", 0x01, 0x02, self::PROTO_V1, self::FRAME_DATA, self::ENCODE_JSON, $len);
    $packet = $header . $data;

    if ($timeout < 300000) {
      $timeout = 300000;
    }

    stream_set_blocking($this->client, true);
    stream_set_timeout($this->client, 0, $timeout);

    return fwrite($this->client, $packet);
  }

  /**
   * @return mixed|string
   * @throws \Exception
   */
  private function _recv()
  {
    $buf = fread($this->client, 9);

    $info = stream_get_meta_data($this->client);
    if ($info['timed_out']) {
      fclose($this->client);
      $this->client = null;
      throw new \Exception("recv timeout");
    }

    $header = unpack("C2/Cversion/Ctype/Cencode/Nsize", substr($buf, 0, 9));
    if ($header[1] != 0x01 && $header[2] != 0x02) {
      fclose($this->client);
      $this->client = null;
      throw new \Exception("invalid packet");
    }

    $packet_length = $header["size"];

    $body = "";
    $buf_size = 4096;

    while (strlen($body) < $packet_length) {
      $buf_size = $packet_length - strlen($body) < 4096 ? $packet_length - strlen($body) : 4096;
      $block = fread($this->client, $buf_size);
      if ($block === false) {
        fclose($this->client);
        $this->client = null;
        throw new \Exception("read packet fail");
      }
      $body .= $block;
    }

    if ($header["encode"] === self::PLAIN_TEXT) {
      return $body;
    }

    return json_decode($body);
  }

  /**
   * @param $success
   * @param $type
   * @param $message
   * @param $data
   * @return \stdClass
   */
  private function _buildResp($success, $type, $message, $data)
  {
    $result = new \stdClass();
    $result->success = $success;
    $result->type = $type;
    $result->message = $message;
    $result->data = $data;

    return $result;
  }

  /**
   * @return mixed|string
   */
  private function _getMillisecond()
  {
    $time = explode(" ", microtime());
    $time = $time [1] . ($time [0] * 1000);
    $time2 = explode(".", $time);
    $time = $time2 [0];

    return $time;
  }

  /**
   * @param $text
   * @param $project
   * @param $key
   * @return mixed
   * @throws \Exception
   */
  public function localCheck($text, $project, $keys, $once = true)
  {
    if (!is_array($keys)) {
      throw new \Exception("keys is not array"); 
    }
    try {
      $hits = [];
      foreach ($keys as $key) {
        $filepath = sprintf($this->conf_path . '/%s/%s', $project, $key);
        $val = file_get_contents($filepath);
        $keywords = json_decode($val, true);
        // 使用正则表达式去匹配敏感词
        $pregStr = implode('|', array_map(function ($keyword) {
          return preg_quote($keyword, '#');
        }, $keywords));
        $str = "#" . $pregStr . '#i';
        preg_match($str, $text, $match);
        if (count($match) > 0) {
          $hits = array_merge($hits, $match);
          if ($once) {
            break;
          }
        }
      }
      
      return $hits;
    } catch (\Exception $e) {
      throw new \Exception(sprintf("localCheck[%s/%s] fail:", $project, $key, $e->getMessage()));
    }
  }

  /**
   * destruct
   */
  public function __destruct()
  {
    if (!is_null($this->client)) {
      fclose($this->client);
    }
  }
}