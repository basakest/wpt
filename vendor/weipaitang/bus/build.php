<?php

(new Build())->run();

class Build
{
    public function run()
    {
        $serviceName = $this->getInputServiceName();
        if (empty($serviceName)) {
            exit("请输出需创建的服务名称！\n demo: php build.php -s order");
        }

        $this->buildApplication($serviceName);
        $this->buildConfig($serviceName);
    }

    private function getInputServiceName()
    {
        $optArr = getopt('s:');
        return ucfirst(trim($optArr['s']));
    }

    private function buildApplication($serviceName)
    {
        $fileName = __DIR__ . "/src/Service/$serviceName/Application.php";
        $this->buildFile($fileName, $this->buildApplicationTemplate($serviceName));
    }

    private function buildApplicationTemplate($serviceName)
    {
        $template = <<<EOF
<?php


namespace WptBus\Service\\$serviceName;

use WptBus\Service\BaseService;

class Application extends BaseService
{
    protected \$serviceName;
    protected \$config = [];

    public function init(\$serviceName, \$config)
    {
        \$this->serviceName = \$serviceName;
        \$this->config = \$config;
    }
}
EOF;
        return str_replace('\$', '$', $template);
    }

    private function buildConfig($serviceName)
    {
        $fileName = __DIR__ . "/src/Config/" . lcfirst($serviceName) . ".php";
        $this->buildFile($fileName, $this->buildConfigTemplate($serviceName));
    }

    private function buildConfigTemplate($serviceName)
    {

        $lcFirstServiceName = lcfirst($serviceName);
        $template = <<<EOF
<?php

namespace WptBus\Config;

return [
    'http' => [
        'name' => '$lcFirstServiceName',
        'servers' => [],
        'balance' => 'mainSpare', // 主备
        'connectTimeout' => 2000, // 连接超时ms
        'readTimeout' => 2000, // 读超时ms
        'debug' => false, // 请求日志记录返回结果
    ]
];
EOF;
        return str_replace("\$", "$", $template);
    }

    private function buildFile($fileName, $template)
    {
        if ($this->alreadyExists($fileName)) {
            exit($fileName . " 文件已经存在!");
        }
        $this->makeDirectory(dirname($fileName));
        $this->createFile($fileName, $template);
    }

    private function alreadyExists($fileName)
    {
        return file_exists($fileName);
    }

    private function makeDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function createFile($fileName, $template)
    {
        $resource = fopen($fileName, "w");
        if (empty($resource)) {
            return false;
        }
        fwrite($resource, $template);
        fclose($resource);
        return true;
    }
}


