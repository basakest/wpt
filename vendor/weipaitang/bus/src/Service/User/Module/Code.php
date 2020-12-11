<?php


namespace WptBus\Service\User\Module;


use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Code extends BaseService
{
    const TEMPLATE_COMMON = "common";
    const TEMPLATE_LOGIN = "login";

    const IMAGE_CODE_SCENE_LOGIN = "login";

    const CALL_TELEPHONE = "951037209";

    // 发送
    public function send(int $nationCode, string $telephone, string $sendType, string $ip, string $template = self::TEMPLATE_COMMON, string $platform = "")
    {
        $header = Utils::getClientInfo($platform);
        $ret = $this->httpPost(Router::CODE_SEND, [
            "nationCode" => (string)$nationCode,
            "telephone" => $telephone,
            "sendType" => $sendType,
            "ip" => $ip,
            "template"=> $template
        ], $header);
        if ($ret["code"] == 202115 || $ret["code"] == 202116 || $ret["code"] == 202118) { // 白名单和10分钟内禁止和天域限制
            $ret["code"] = 0;
            $ret["msg"] = $ret["msg"];
            $ret["data"]["codeType"] = $sendType;
            $ret["data"]["voiceNumber"] = $sendType == "call" ? self::CALL_TELEPHONE : "";
        }
        return $ret;
    }

    // 验证
    public function verify(string $nationCode, string $telephone, string $code, string $template = self::TEMPLATE_COMMON)
    {
        $ret = $this->httpPost(Router::CODE_VERIFY, [
            "nationCode" => (string)$nationCode,
            "telephone" => $telephone,
            "code" => $code,
            "template"=> $template
        ]);
        if ($ret["code"] == 202116) { // 10分钟内禁止验证 提示 验证码错误
            $ret["code"] = 202029;
            $ret["msg"] = "验证码错误";
        }
        return $ret;
    }

    // 验证通过全手机号
    public function verifyByWholePhone(string $wholePhone, string $code, string $template = self::TEMPLATE_COMMON)
    {
        return $this->verify("", $wholePhone, $code, $template);
    }

    /**
     * 需要检查图形验证码
     * @param string $nationCode
     * @param string $telephone
     * @return array
     */
    public function isCheckImageCode(string $nationCode, string $telephone, string $scene = self::IMAGE_CODE_SCENE_LOGIN)
    {
        $params = ["nationCode" => $nationCode, "telephone" => $telephone, "scene" => $scene];
        return $this->httpPost(Router::IS_CHECK_IMAGE_CODE, $params);
    }

    /**
     * 检查图形验证码
     * @param string $nationCode
     * @param string $telephone
     * @return array
     */
    public function verifyImageCode(string $nationCode, string $telephone, string $ticket, string $randStr, string $ip, string $scene = self::IMAGE_CODE_SCENE_LOGIN)
    {
        $params = [
            "scene" => $scene,
            "nationCode" => $nationCode,
            "telephone" => $telephone,
            "ticket" => $ticket,
            "randStr" => $randStr,
            "ip" => $ip
        ];
        return $this->httpPost(Router::VERIFY_IMAGE_CODE, $params);
    }

    /**
     * 获取发送状态
     * @param string $nationCode
     * @param string $telephone
     * @return array
     */
    public function getSendStatus(string $nationCode, string $telephone, string $sendType, string $ip, int $signR, string $template = self::TEMPLATE_LOGIN)
    {
        $params = [
            "nationCode" => $nationCode,
            "telephone" => $telephone,
            "sendType" => $sendType,
            "ip" => $ip,
            "signR" => $signR,
            "template" => $template,
        ];
        return $this->httpPost(Router::CODE_GET_SEND_STATUS, $params);
    }
}