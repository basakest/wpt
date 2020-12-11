<?php

namespace WptUtilsTest;

use WptUtils\Http\Client;
use WptUtils\Poster\Poster;

class PosterTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTextWidth()
    {
        $param = [
            'tplName' => 'one_one',
        ];

        $config = Client::instance()->asJson()->post("http://10.3.7.20:8080/dcapi/poster/get", json_encode($param))();
        $config = json_decode($config, true);

        $configArr = json_decode($config['data'], true);

        $opt = [];
        $cfg = [];
        foreach ($configArr as $item) {
            foreach ($item as $k => $value) {
                if (preg_match('/#.*#/', $value)) {
                    $cfg[trim($value, '#')] = $item['content'];
                    $opt[$value] = $item['content'];
                }
            }
        }

        $cfg['text_8'] = '店铺保证金 5,000';
        $cfg['text_1'] = "打发斯蒂芬七十多分阿萨德";
        $cfg['text_2'] = '一口价出价功能测一口价出价功能测一口价出价功能测一口价出价功能测';


        $res = (new Poster())
            ->setTextWidthServer('http://draw.weipaitang.com/gd/text/width')
            ->setEndpoint("http://10.3.7.20:8080")
            ->setImgServer('http://10.3.0.14:8080/img')
            ->setProperties($cfg)
            ->make('one_one');
        print_r($res);
        exit;
    }
}