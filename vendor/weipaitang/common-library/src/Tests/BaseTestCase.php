<?php

namespace WptCommon\Library\Tests;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected $filePrefix = 'test';
    protected $fileSystem;
    protected $logFile;
    protected $message;

    public function setUp()
    {
        $this->fileSystem = new Filesystem();
        !defined('ROOT_PATH') and define('ROOT_PATH', realpath(dirname(__DIR__) . '/../'));
        $this->logFile = $this->getLogFile();
        $this->setRandomMessage();
    }

    protected function getLogFile()
    {
        return constant('ROOT_PATH') . '/storage/newlogs/' . $this->filePrefix . '-' . date('Y-m-d') . '.log';
    }

    protected function setRandomMessage()
    {
        $this->message = Str::random();
    }

    protected function getLastLog()
    {
        try {
            $log = $this->fileSystem->get($this->logFile);
            $lastLog = collect(explode(PHP_EOL, $log))->filter()->last();
        } catch (FileNotFoundException $e) {
        }
        return json_decode($lastLog ?? '');
    }
}
