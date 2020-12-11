<?php


namespace WptCommon\Library\Tools\Transport;


use WptCommon\Library\Tools\Logger;

class IExecutor
{
    /** @var Resource */
    public $resource;
    /** @var Logger */
    public $logger;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->logger = $this->getLogger();
    }

    public function getLogger()
    {
        $config = ["mlogger" =>
            [
                "logs_dir" => "newlogs/micro-sdk",
                "expand_fields" => ["name", "url", "unique_id", "curlerrno", "httpcode"],
                "log_level" => "info"
            ]
        ];
        return new Logger($config);
    }
}