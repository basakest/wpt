<?php


namespace WptUtils\Poster;

use WptUtils\Exception\HttpException;
use WptUtils\Http\Client;
use WptUtils\Logger\Logger;

/**
 * Class Poster
 *
 * @package WptUtils\Poster
 */
class Poster
{
    /**
     * @const
     */
    const POSTER_ERR = 71001;

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * @var $endpoint
     */
    protected $endpoint;

    /**
     * @var $imgServer
     */
    protected $imgServer;

    /**
     * @var $textWidthServer
     */
    protected $textWidthServer;

    /**
     * @var array $properties
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $contentTextWtdth = [];

    /**
     * @var $originalTextWidth
     */
    protected $originalTextWidth = [];

    /**
     * @var array
     */
    protected $customEle = [];

    /**
     * @var bool
     */
    protected $hasLf = false;

    /**
     * @var int $bgWidth
     */
    protected $bgWidth = 0;

    /**
     * @var int $bgheight
     */
    protected $bgheight = 0;

    /**
     * @var array $plugin
     */
    protected $plugin = [];

    /**
     * 内置插件 微信小程序码
     */
    const WX_MINI_PROGRAM = 'wxMiniprogram';

    /**
     * 文字宽度地址
     */
    const DRAW_TEXT_WIDTH_URL = 'http://10.3.7.46:8080/gd/text/width';

    /**
     * 画图地址
     */
    const DRAW_URL = 'http://10.3.7.46:8080/img';

    /**
     * 服务地址
     */
    const MICRO_GATEWAY = 'http://10.3.7.20:8080';

    /**
     * @return static|null
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        self::$instance->properties = [];
        self::$instance->contentTextWtdth = [];
        self::$instance->originalTextWidth = [];
        self::$instance->customEle = [];
        return self::$instance;
    }


    /**
     * @param $tplName
     * @param null $callback
     * @return string|mixed
     * @throws HttpException
     */
    public function make($tplName, $callback = null)
    {
        $st = microtime(true);
        // 获取配置
        $template = $this->getConfig($tplName);
        // 额外标签
        $template = $this->addExtraTag($template);
        // 计算原始高度
        $template = $this->calcOriginalHeight($template);
        // 替换内容
        $template = $this->replaceTemplateData($template);
        // 计算新位置
        $poster = $this->calcNewPosition($template);
        // 处理自定义标签
        $poster = $this->handleCustomTag($poster);
        // 加载插件
        $poster = $this->loadPlugin($poster);
        // 自定义回调
        if (is_callable($callback) && !is_null($callback)) {
            $poster = $callback($poster);
        }
        // 重置背景
        $poster = $this->resetBg($poster);


        // 生成图片
        $queryString = $this->createLink($poster);
        $url = sprintf("%s?%s", $this->getImgServer(), $queryString);

        $errCode = -1;
        try {
            $cli = Client::instance()->setTimeout(3000)->setRetries(0)->get($url);
            $httpCode = $cli->getHttpStatusCode();
            if ($httpCode == 200) {
                $result = $cli->getResponse();
                $result = json_decode($result, true);
                if (isset($result['code']) && $result['code'] == 0) {
                    Logger::info('wptUtilsPoster', '海报生成', [
                        'tplName' => $tplName,
                        'extend2' => round(microtime(true) - $st, 2) * 1000
                    ], true);
                    return $result['data']['shareQRUrl'] ?? '';
                }
                $errMsg = [$result];
            } else {
                $errMsg = ($result['msg'] ?? trim($cli->getResponse())) ?: "未知错误";
            }
        } catch (\Throwable $e) {
            $errCode = $e->getCode();
            $errMsg = $e->getMessage();
        }

        $msg = [
            'msg' => $errMsg,
            'code' => $result['code'] ?? self::POSTER_ERR,
            'args' => func_get_args(),
            'tplName' => $tplName,
            'properties' => $this->properties,
        ];

        if ($errCode > 0) {
            $msg['code'] = $errCode;
            Logger::warning("wptUtilsPosterException", '海报生成异常', $msg);
        } else {
            Logger::error("wptUtilsPosterErr", '海报生成失败', $msg);
        }
        throw new HttpException(
            "生成海报服务报错: \n" . json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            self::POSTER_ERR
        );
    }

    /**
     * @param $poster
     * @param $url
     * @return mixed
     */
    private function wxMiniprogram($poster, $url)
    {
        foreach ($poster as &$item) {
            if ($item['type'] == 'qr') {
                $item['w'] = $item['size'];
                $item['h'] = $item['size'];
                $item['type'] = 'pic';
                $item['path'] = $url;
                unset($item['t']);
                unset($item['logo']);
                unset($item['size']);
            }
        }
        return $poster;
    }

    /**
     * @param $plugin
     * @param mixed ...$value
     * @return static
     */
    public function plugin($plugin, ...$value)
    {
        $this->plugin[] = [$plugin, $value];
        return $this;
    }

    /**
     * @param $tplName
     * @return mixed|string
     * @throws HttpException
     */
    public function preview($tplName)
    {
        // 获取配置
        $template = $this->getConfig($tplName);

        $flag = [];
        foreach ($template as $item) {
            foreach ($item as $k => $v) {
                if (preg_match('/^#.*#$/', $v)) {
                    $flag[trim($v, '#')] = $item['content'];
                }
            }
        }
        $this->setProperties($flag);
        $template = $this->replaceTemplateData($template);
        $template = $this->removeAssistTag($template);

        // 生成图片
        $queryString = $this->createLink($template);
        $url = sprintf("%s?%s", $this->getImgServer(), $queryString);
        try {
            $result = Client::instance()->get($url)->getResponse();
            $result = json_decode($result, true);
            if (isset($result['code']) && $result['code'] == 0) {
                return $result['data']['shareQRUrl'] ?? '';
            }
            $errMsg = $result['msg'] ?? 'unknown';
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
        }

        $msg = [
            'msg' => $errMsg,
            'code' => $result['code'] ?? self::POSTER_ERR,
            'args' => func_get_args(),
        ];
        throw new HttpException(
            "生成海报预览错误: \n" . json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            self::POSTER_ERR
        );
    }

    /**
     * @param $width
     * @param $height
     * @return $this
     * @throws HttpException
     */
    public function resizeBackground($width, $height)
    {
        $this->bgWidth = $width;
        $this->bgheight = $height;

        if ($width > 3200 || $height > 3200) {
            throw new HttpException("背景大小错误 最大[3200 * 3200]", self::POSTER_ERR);
        }

        return $this;
    }

    private function resetBg($poster)
    {
        if ($this->bgheight > 0 && $this->bgWidth > 0) {
            foreach ($poster as &$p) {
                if (isset($p['type']) && $p['type'] == 'bg') {
                    $p['w'] = $this->bgWidth;
                    $p['h'] = $this->bgheight;
                    break;
                }
            }
        }
        return $poster;
    }

    /**
     * 用于自定义元素
     *
     * @param $template
     * @return mixed
     */
    private function addExtraTag($template)
    {
        foreach ($template as &$item) {
            if ($item['type'] == 'bg') {
                continue;
            }
            if ($item['type'] == 'text') {
                $flag = trim($item['t'], '#');
                $item['extra'] = "@{$flag}@";
            } elseif ($item['type'] == 'qr') {
                $flag = trim($item['t'], '#');
                $item['extra'] = "@{$flag}@";
            } else {
                $flag = trim($item['path'], '#');
                $item['extra'] = "@{$flag}@";
            }
        }
        return $template;
    }

    /**
     * 处理自定义标签
     *
     * @param $poster
     * @return mixed|array
     */
    private function handleCustomTag($poster)
    {
        foreach ($this->customEle as $key => $val) {
            foreach ($poster as &$item) {
                if (isset($item['extra']) && (trim($key) == trim($item['extra']))) {
                    $append = [];
                    foreach ($val as $k => $handler) {
                        if (is_array($handler)) {
                            list($a, $b) = explode(".", $handler[0]);
                            $fn = $handler[1] ?? null;
                            $target = 0;
                            foreach ($poster as $p) {
                                if (isset($p['extra']) && "@{$a}@" == trim($p['extra'])) {
                                    if ($b == 'width') {
                                        $b = 'text_width';
                                    } elseif ($b == 'height') {
                                        $b = 'tpl_height';
                                    }
                                    $target = $p[$b] ?? null;
                                    break;
                                }
                            }
                            if (!is_null($fn)) {
                                $target = $fn($target);
                            }
                            $append[$k] = $target;
                        } else {
                            $append = $val;
                        }
                    }
                    $item = array_merge($item, $append);
                }
            }
        }
        foreach ($poster as &$p) {
            if (isset($p['extra'])) {
                unset($p['extra']);
            }
            unset($p['text_width']);
            unset($p['tpl_height']);
            if (isset($p['font']) && !empty($p["font"])) {
                $font = urldecode($p['font']);
                $p['font'] = urlencode($font);
            }
        }
        return $poster;
    }

    /**
     * 加载插件
     *
     * @param $poster
     * @return mixed
     * @see wxMiniprogram()
     */
    private function loadPlugin($poster)
    {
        foreach ($this->plugin as $plugin) {
            $internal = $plugin[0] ?? '';
            $params = $plugin[1] ?? '';
            if (empty($internal)) {
                throw new \InvalidArgumentException("无效的插件参数");
            }
            $poster = call_user_func([$this, $internal], $poster, ...$params);
        }
        return $poster;
    }

    /**
     * @param array $template
     * @return array
     */
    private function removeAssistTag(array $template)
    {
        $removeHeight = 0;
        // 清理所有辅助参数
        foreach ($template as $k => &$item) {
            if (isset($item['remove_dy'])) {
                $removeHeight += $item['remove_dy'];
            }

            unset($item['sort']);
            unset($item['line']);
            unset($item['desc']);
            unset($item['fixed']);
            // unset($item['tpl_height']);
            unset($item['actual_width']);
            // unset($item['text_width']);
            unset($item['content']);
            unset($item['group']);
            unset($item['x']);
            unset($item['y']);
            unset($item['remove_dy']);
            unset($item['relative']);
            unset($item['ctx']);

            // 删除没有数据的图片
            if ($item['type'] == 'pic' && ($item['path'] === '' || preg_match('/^#.*#$/', $item['path']))) {
                unset($template[$k]);
            }
            // 删除没有数据的文本
            if ($item['type'] == 'text' && ($item['t'] === '' || preg_match('/^#.*#$/', $item['t']))) {
                unset($template[$k]);
            }
            // 删除没有数据的文本
            if ($item['type'] == 'qr' && ($item['t'] === '' || preg_match('/^#.*#$/', $item['t']))) {
                unset($template[$k]);
            }

            if (!empty($item['path'])) {
                $item['path'] = urlencode($item['path']);
            }
            if (!empty($item['t'])) {
                $item['t'] = urlencode($item['t']);
            }
            if (!empty($item['c'])) {
                $color = urldecode($item['c']);
                $item['c'] = urlencode($color);
            }

            if ($item['type'] == 'qr') {
                if (isset($item['logo']) && ($item['logo'] == false || empty($item['logo']))) {
                    $item['logo'] = 'false';
                }
            }
        }

        foreach ($template as &$tpl) {
            if ($tpl['type'] == 'bg') {
                $tpl['h'] -= $removeHeight;
            }
        }

        return $template;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties($properties = [])
    {
        foreach ($properties as $k => $v) {
            if (is_array($v)) {
                if (isset($v['lf']) && $v['lf']) {
                    $this->hasLf = true;
                    $this->properties["#{$k}#"] = $this->filterSpecialSymbol($v['ctx']);
                    unset($v['ctx']);
                    unset($v['lf']);
                    $this->hasLf = false;
                    $this->customEle["@{$k}@"] = $v;
                } else {
                    $str = strval(trim($v['ctx']));
                    $str = $this->filterSpecialSymbol(
                        addslashes($str)
                    );

                    $this->properties["#{$k}#"] = $str;
                    unset($v['ctx']);
                    $this->customEle["@{$k}@"] = $v;
                }
            } else {
                $flagkey = sprintf("#%s#", $k);
                $str = strval(trim($v));
                $str = $this->filterSpecialSymbol(
                    addslashes($str)
                );
                $this->properties[$flagkey] = $str;
            }
        }
        return $this;
    }

    /**
     * 计算原始高度
     *
     * @param $template
     * @return array
     * @throws HttpException
     */
    private function calcOriginalHeight($template)
    {
        // 所有文本原始宽度
        $allTextWidth = $this->mulitGetTextWidth($template);
        $this->originalTextWidth = $allTextWidth;

        $fixed = [];
        $flexibility = [];
        foreach ($template as $k => $item) {
            if ($item['type'] == 'bg') {
                $item['fixed'] = true;
            }
            $item['sort'] = $k;
            if (isset($item['fixed']) && $item['fixed']) {
                $fixed[$k] = $item;
            } else {
                $flexibility[$k] = $item;
            }
        }
        // 计算模板原始高度
        foreach ($flexibility as &$item) {
            $itemHeight = in_array($item['type'], ['text', 'qr']) ? $item['size'] : $item['h'];
            if ($item['type'] == 'text') {
                $textWidth = $allTextWidth[trim($item['content'])] ?? -1;
                if ($textWidth == -1) {
                    $itemTextWidth = $this->checkTextWidth($item['content'], $item['font'], $item['size']);
                } else {
                    $itemTextWidth = $textWidth;
                }
                $item['actual_width'] = $itemTextWidth;
                if ($itemTextWidth > $item['width'] && $item['width'] != 0 && $item['height'] != 0) {
                    $line = ceil($itemTextWidth / $item['width']);
                    $item['tpl_height'] = floor($itemHeight * $line * 1.3);
                    $item['line'] = $line;
                } else {
                    $item['tpl_height'] = floor($itemHeight * 1.3);
                    $item['line'] = 1;
                }
            } else {
                $item['tpl_height'] = $itemHeight;
            }
        }

        // 数据合并
        $template = array_merge($fixed, $flexibility);

        // 数据排序
        $template = collect($template)->sortBy('sort')->toArray();
        return array_values($template);
    }

    /**
     * @param $poster
     * @return array
     * @throws HttpException
     */
    private function calcNewPosition($poster)
    {
        $flexibility = [];
        $fixed = [];

        foreach ($poster as $k => $item) {
            if (isset($item['fixed']) && $item['fixed']) {
                $fixed[$k] = $item;
            } else {
                $flexibility[$k] = $item;
            }
        }

        // 所有新文本宽度
        $this->contentTextWtdth = $this->mulitGetTextWidth($poster, 't');
        if ($flexibility) {
            // 计算y轴
            $flexibility = array_values(collect($flexibility)->sortBy('dy')->toArray());
            $flexibility = $this->calcDy($flexibility);
            // 计算x轴
            $flexibility = array_values(collect($flexibility)->sortBy('dx')->toArray());
            $flexibility = $this->calcDx($flexibility);
        }

        $flexibility = array_values($flexibility);
        $poster = array_merge($fixed, $flexibility);

        $template = collect($poster)->sortBy('sort')->toArray();
        $poster = array_values($template);

        return $this->removeAssistTag($poster);
    }

    /**
     * 替换模板数据
     *
     * @param $templateConfig
     * @return array
     */
    private function replaceTemplateData($templateConfig)
    {
        $config = str_replace(
            array_keys($this->properties),
            array_values($this->properties),
            json_encode($templateConfig)
        );

        $config = json_decode($config, true);

        foreach ($config as $k => &$item) {
            if (isset($item['relative']) && $item['relative'] == true) {
                continue;
            }
            if ($item['type'] == 'text' && ($item['t'] === '' || preg_match('/^#.*#$/', $item['t']))) {
                unset($config[$k]);
            }
            if ($item['type'] == 'pic' && ($item['path'] == '' || preg_match('/^#.*#$/', $item['path']))) {
                $item['remove_dy'] = $item['h'];
            }
        }
        return array_values($config);
    }

    /**
     * @param $poster
     * @return array
     * @throws HttpException
     */
    private function calcDy($poster)
    {
        $all = [];
        if (count($poster) == 0) {
            return $all;
        }
        $head = array_shift($poster);
        $itemHeight = in_array($head['type'], ['text', 'qr']) ? $head['size'] : $head['h'];

        if (isset($head['remove_dy'])) {
            foreach ($poster as $k => &$item) {
                $item['dy'] -= $head['remove_dy'];
            }
        }

        if ($head['type'] == 'text') {
            // 当前模板高度
            // $itemTextWidth = $this->checkTextWidth($head['t'], $head['font'], $head['size']);
            $textWidth = $this->contentTextWtdth[trim($head['t'])] ?? -1;
            if ($textWidth == -1) {
                $itemTextWidth = $this->checkTextWidth($head['t'], $head['font'], $head['size']);
            } else {
                $itemTextWidth = $textWidth;
            }
            $head['text_width'] = $itemTextWidth;
            if ($itemTextWidth > $head['width'] && $head['width'] != 0 && $head['height'] != 0) {
                $maxLine = floor($head['height'] / $head['size']);
                $line = ceil($itemTextWidth / $head['width']);
                $line = min($maxLine, $line);
                $curHeight = $itemHeight * $line;
            } else {
                $curHeight = $itemHeight;
            }

            foreach ($poster as $k => &$item) {
                $item['dy'] += -($head['tpl_height'] - floor($curHeight * 1.3));
            }
        }

        return array_merge($all, [$head], $this->calcDy($poster));
    }

    /**
     * @param $poster
     * @return array
     * @throws HttpException
     */
    private function calcDx($poster)
    {
        $all = [];
        if (count($poster) == 0) {
            return $all;
        }
        $head = array_shift($poster);

        if ($head['type'] == 'text') {
            $textWidth = $this->contentTextWtdth[trim($head['t'])] ?? -1;
            if ($textWidth == -1) {
                $itemTextWidth = $this->checkTextWidth($head['t'], $head['font'], $head['size']);
            } else {
                $itemTextWidth = $textWidth;
            }
            $originalTextWidth = $this->originalTextWidth[$head['content']];
            if ($itemTextWidth > $head['width'] && $head['width'] != 0) {
                $itemTextWidth = $head['width'];
            }
            $w = $itemTextWidth - $originalTextWidth;
            foreach ($poster as $k => &$item) {
                if (isset($item['group']) && isset($head['group'])) {
                    if ($item['group'] == $head['group']) {
                        $item['dx'] += $w;
                    }
                }
            }
        }
        return array_merge($all, [$head], $this->calcDx($poster));
    }

    /**
     * @param $tplName
     * @return array
     * @throws HttpException
     */
    private function getConfig($tplName): array
    {
        if (empty($tplName)) {
            throw new HttpException("参数不能为空", self::POSTER_ERR);
        }

        $url = sprintf("%s%s", $this->getEndpoint(), Route::POSTER_GET);
        $param = [
            'tplName' => $tplName,
        ];

        try {
            $result = Client::instance()->asJson()->setTimeout(2000)->setRetries(1)->post($url, json_encode($param))();
            $result = json_decode($result, true);
            if (isset($result['code']) && $result['code'] == 0) {
                return json_decode($result['data'] ?? '{}', true);
            }
            $errMsg = $result['msg'] ?? 'unknown';
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
        }
        $msg = [
            'msg' => $errMsg,
            'code' => $result['code'] ?? self::POSTER_ERR,
            'args' => func_get_args(),
        ];
        throw new HttpException(
            "获取海报配置报错: \n" . json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            self::POSTER_ERR
        );
    }

    /**
     * @param $text
     * @param $font
     * @param $size
     * @return mixed|string
     * @throws HttpException
     */
    private function checkTextWidth($text, $font, $size)
    {
        $param = [
            't' => $text,
            'font' => $font,
            'size' => $size,
        ];
        $url = sprintf("%s?%s", $this->getTextWidthServer(), http_build_query($param));

        try {
            $result = Client::instance()->setTimeout(2000)->setRetries(0)->get($url)();
            $result = json_decode($result, true);
            if (isset($result['code']) && $result['code'] == 0) {
                return $result['data']['width'] ?? '';
            }
            $errMsg = $result['msg'] ?? 'unknown';
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
        }

        $msg = [
            'msg' => $errMsg,
            'code' => $result['code'] ?? self::POSTER_ERR,
            'args' => func_get_args(),
        ];
        throw new HttpException(
            "获取海报文字宽度错误: \n" . json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            self::POSTER_ERR
        );
    }

    /**
     * @param $poster
     * @return false|string
     */
    private function createLink($poster)
    {
        $url = '';
        foreach ($poster as $k => $v) {
            foreach ($v as $key => $val) {
                $url .= $key . '=' . $val . '&';
            }
            $url = substr($url, 0, -1);
            $url .= '|';
        }
        $url = substr($url, 0, -1);
        return $url;
    }

    /**
     * @param $template
     * @param string $field
     * @param bool $md5
     * @return array
     * @throws HttpException
     */
    private function mulitGetTextWidth($template, $field = 'content', $md5 = false)
    {
        $texts = [];
        foreach ($template as $item) {
            if ($item['type'] == 'text' && $item[$field] !== 0 && $item[$field] !== "") {
                $texts[] = [
                    't' => $item[$field],
                    'font' => $item['font'],
                    'size' => $item['size']
                ];
            }
        }
        if (empty($texts)) {
            return [];
        }

        $client = new Client();
        foreach ($texts as $text) {
            $client->add(function (Client $client) use ($text) {
                $textReq = [
                    't' => $text['t'],
                    'font' => $text['font'],
                    'size' => $text['size']
                ];
                $client->setKey($text['t'])->get($this->getTextWidthServer(), $textReq);
            });
        }
        $result = $client->start()->getResponse();
        $textWidths = [];
        foreach ($result as $key => $val) {
            $res = json_decode($val, true);
            $k = $md5 ? md5(trim($key)) : trim($key);
            $code = $res['code'] ?? -1;
            if ($code == 0) {
                $textWidths[$k] = $res['data']['width'] ?? -1;
            } else {
                $textWidths[$k] = -1;
            }
        }
        return $textWidths;
    }

    /**
     * @return mixed
     */
    private function getImgServer()
    {
        $host = $this->imgServer == '' ? env("DRAW_URL", self::DRAW_URL) : $this->imgServer;
        return rtrim($host, '/');
    }

    /**
     * @param mixed $imgServer
     * @return Poster
     */
    public function setImgServer($imgServer)
    {
        $this->imgServer = $imgServer;
        return $this;
    }


    /**
     * @return mixed
     */
    private function getEndpoint()
    {
        return rtrim(
            $this->endpoint == ''
                ? env("MICRO_GATEWAY", self::MICRO_GATEWAY)
                : $this->endpoint,
            '/'
        );
    }

    /**
     * @param mixed $endpoint
     * @return Poster
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getTextWidthServer()
    {
        $url = $this->textWidthServer == ''
            ? env("DRAW_TEXT_WIDTH_URL", self::DRAW_TEXT_WIDTH_URL)
            : $this->textWidthServer;

        return rtrim($url, '/');
    }

    /**
     * @param mixed $textWidthServer
     * @return Poster
     */
    public function setTextWidthServer($textWidthServer)
    {
        $this->textWidthServer = $textWidthServer;
        return $this;
    }

    /**
     * @param $str
     * @return string
     */
    private function filterSpecialSymbol($str)
    {
        if ($this->hasLf) {
            $search = array("\n", "\r", "\t", "\'");
            $replace = array("\\n", "", "", "'");
        } else {
            $search = array("\n", "\r", "\t", "\'");
            $replace = array("", "", "", "'");
        }
        return str_replace($search, $replace, $str);
    }
}
