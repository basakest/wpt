<?php
/**
 * @auther heyu 2020/6/30
 */

namespace App\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WptCommon\Library\Facades\MLogger;

class Ding
{
    /**
     * 向企业微信发送提醒消息
     *
     * @param string $title 发送标题
     * @param string $content 发送内容
     * @return void
     */
    public static function wechatGroup(string $title, string $content)
    {
        $accessToken = env('WECHAT_KEY', '69e51416-286a-48b4-9e0b-b19d20f247f1');
        $webHookUrl = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=' . $accessToken;
        $content = 'ENV:' . env('APP_ENV') . "\n" . $content;
        if (strlen($content) > 4000) {
            $content = substr($content, 0, 4000) . '...';
        }
        $postData = ['msgtype' => "markdown", 'markdown' => compact('title', 'content')];

        try {
            $result = self::sendToWechatHook($webHookUrl, $postData);

            // 发送失败，记录错误日志
            if ($result !== true) {
                MLogger::error('ding_error', '企业微信消息发送[failed1]', [
                    'result' => $result,
                    'postUrl' => $webHookUrl,
                    'postData' => $postData,
                ]);
            }
        } catch (GuzzleException $e) {
            // 请求失败，记录错误日志
            MLogger::error('ding_error', '企业微信消息发送[failed2]', [
                'errorMsg' => $e->getMessage(),
                'postUrl' => $webHookUrl,
                'postData' => $postData,
            ]);
        }
    }

    /**
     * 发送信息到企业微信 Hook
     *
     * @param string $hook Hook 网址
     * @param array $data 待发送的数据
     * @return bool|string
     * @throws GuzzleException
     */
    private static function sendToWechatHook(string $hook, array $data)
    {
        $client = new Client();
        $response = $client->request('POST', $hook, ['timeout' => 1, "json" => $data]);
        $result = $response->getBody()->getContents();

        if (!empty($result)) {
            $resultJson = json_decode($result, true);

            if (($resultJson['errcode'] ?? -1) == 0) {
                return true;
            }
        }

        return $result;
    }
}
