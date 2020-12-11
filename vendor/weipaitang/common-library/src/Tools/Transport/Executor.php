<?php


namespace WptCommon\Library\Tools\Transport;


class Executor
{
    /**
     * @param array $httpConf
     * @return HttpExecutor
     */
    public static function loadHttpExecutor($httpConf = [])
    {
        $resource = new Resource($httpConf);
        $httpExecutor = new HttpExecutor($resource);
        return $httpExecutor;
    }
}