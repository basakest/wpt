<?php


namespace WptUtils;

use WptUtils\Logger\Logger;

class Image
{
    /**
     * 原方法ImageBufferUtil::putfile
     * @param $param  ['file' => '', 'filename' => '123123.png']
     * @return bool|mixed
     */
    public static function putFile($param)
    {
        $delimiter = uniqid();
        $url = env("DRAW_UPLOAD_URL", "http://draw-vpc.wptqc.com/upload");
        // $url = 'http://10.3.0.14:8080/upload';

        $post_data = static::buildData($param, $delimiter);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data; boundary=" . $delimiter,
            "Content-Length: " . strlen($post_data)
        ]);
        $response = curl_exec($curl);
        $errorCode = curl_errno($curl);
        $errorMsg = curl_error($curl);
        curl_close($curl);
        if ($errorCode > 0) {
            Logger::info('imageBufferPost', 'curl_error_info', ['data' => $param, 'msg' => $errorMsg]);
            return false;
        }
        $info = json_decode($response, true);
        if (isset($info['code']) && $info['code'] != 0) {
            Logger::info('imageBufferPost', 'code_error_info', ['data' => $param, 'msg' => '程序异常']);
            return false;
        }
        return $info['Data'][$param['filename']];
    }

    private static function buildData($param, $delimiter)
    {
        $data = '';
        $eof = "\r\n";
        $upload = $param['file'];
        unset($param['file']);

        foreach ($param as $name => $content) {
            $data .= "--" . $delimiter . "\r\n"
                . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
                . $content . "\r\n";
        }
        // 拼接文件流
        $data .= "--" . $delimiter . $eof
            . 'Content-Disposition: form-data; name="filename"; filename="' . $param['filename'] . '"' . "\r\n"
            . 'Content-Type:application/octet-stream'."\r\n\r\n";

        $data .= $upload . "\r\n";
        $data .= "--" . $delimiter . "--\r\n";
        return $data;
    }
}
