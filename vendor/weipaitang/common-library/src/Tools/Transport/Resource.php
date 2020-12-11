<?php


namespace WptCommon\Library\Tools\Transport;


class Resource
{
    const BALANCE = [
        'mainSpare' => 'mainSpare',
        'multiMain' => 'multiMain'
    ];

    public $name;
    public $balance;
    public $servers;
    public $serversBak;
    public $retry;
    public $connectTimeout;
    public $readTimeout;
    public $writeTimeout;
    public $debug;
    public $balanceRetry;
    public $token;

    public function __construct($config)
    {
        $this->init($config);
    }

    public function init($config)
    {
        $this->name = $config['name'] ?? "";
        $this->balance = $config['balance'] ?? "";
        $this->servers = $config['servers'] ?? [];
        $this->serversBak = $this->servers;
        $this->retry = $config['retry'] ?? 0;
        $this->connectTimeout = $config['connectTimeout'] ?? 100;
        $this->readTimeout = $config['readTimeout'] ?? 200;
        $this->writeTimeout = $config['writeTimeout'] ?? 200;
        $this->debug = $config['debug'] ?? false;
        $this->token = $config['token'] ?? "";
    }

    public function selectServer()
    {
        if (empty($this->servers) && empty($this->serversBak)) {
            return [];
        }

        switch ($this->balance) {
            case self::BALANCE['mainSpare']:
                $serverCount = count($this->servers);
                $index = $this->balanceRetry % $serverCount;
                $server = $this->servers[$index];
                $this->balanceRetry++;
                break;
            case self::BALANCE['multiMain']:
                if (empty($this->servers)) {
                    $this->servers = $this->serversBak;
                }
                $randKey = array_rand($this->servers);
                $server = $this->servers[$randKey];
                unset($this->servers[$randKey]);
                $this->balanceRetry++;
                break;
            default:
                $server = $this->servers[array_rand($this->servers)];
        }

        return $server;
    }
}