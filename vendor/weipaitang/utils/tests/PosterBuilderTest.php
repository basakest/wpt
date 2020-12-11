<?php

namespace WptUtilsTest;

use PHPUnit\Framework\TestCase;
use WptUtils\Exception\HttpException;
use WptUtils\Poster\PosterBuilder;

class PosterBuilderTest extends TestCase
{

    public $reqUrl = 'http://10.3.0.14:8080/img';

    public function testPoster()
    {
        $fiedls = array(
            '#bg_1#' => 'https://cdn01t.weipaitang.com/img/20200325e2ppnxcq-auki-90sv-gjlc-0mdalvivbucq-W441H442/w/640',
            '#mat_1#' => 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322',
            '#mat_2#' => 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322',
            '#text_1#' => '我是文案1',
            '#text_2#' => '我是文案2',
            '#qr_1#' => 'https://www.weipaitang.com',
            '#mat_3#' => 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322',
        );

        try {
            $link = PosterBuilder::instance()
                ->setEndpoint("http://localhost:8080")
                ->set('bg_1', 'https://cdn01t.weipaitang.com/img/20200325e2ppnxcq-auki-90sv-gjlc-0mdalvivbucq-W441H442/w/640')
                ->set('mat_1', 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322')
                ->set('mat_2', 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322')
                ->set('text_1', '我是文案1')
                ->set('text_2', '我是文案2')
                ->set('qr_1', 'http://w.weipaitang.com')
                ->set('mat_3', 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322')
                ->make("sale_poster");
            var_export($link);

        } catch (HttpException $e) {
            var_dump($e->getMessage());
        }
        die;
    }

    /**
     * @throws HttpException
     */
    public function testPosterGet()
    {
        $a = array(
            'pic_1' => 'https://cdn01t.weipaitang.com/img/20200407u1hmwwai-75so-tvze-5z15-oynukyk8esad-W1125H1500/w/640',
            'pic_2' => 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322',
            'text_1' => '已鉴定你是今年春季开始你的错',
            'text_2' => '一口价出价功能测试',
            'qr_1' => 'http://t-s.wpt.la/iqq4yO',
            'text_3' => '￥',
            'text_4' => '起',
            'text_5' => '0',
        );

        $instance = PosterBuilder::instance();
        $instance->setArray($a);
        $link = $instance->setEndpoint("http://localhost:8080")->setImgServer('http://10.3.0.14:8080/img')->get('one_pic');
        var_dump($link);

        die;
    }

    public function testOnline()
    {
        $a = array(
            'pic_1' => 'https://cdn01t.weipaitang.com/img/20200407u1hmwwai-75so-tvze-5z15-oynukyk8esad-W1125H1500/w/640',
            'pic_2' => 'https://cdn.weipaitang.com/static/201908165797a9f9-9e1e-4e13-8725-49809c6f4c86-W690H322',
            'text_1' => '已鉴定你是今年春季开始你的错',
            'text_2' => '一口价出价功能测试',
            'qr_1' => 'http://t-s.wpt.la/iqq4yO',
            'text_3' => '￥',
            'text_4' => '起',
            'text_5' => '0',
        );

        $instance = PosterBuilder::instance();
        $instance->setArray($a);
        $link = $instance->setEndpoint("http://10.3.7.20:8080")->setImgServer('http://10.3.0.14:8080/img')->get('one_one');
        var_dump($link);
    }
}