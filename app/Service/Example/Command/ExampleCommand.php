<?php


namespace App\Service\Example\Command;

use Illuminate\Console\Command;
use WptBus\Facades\Bus;

class ExampleCommand extends Command
{
    /**
     * 命令名
     *
     * @var string
     */
    protected $signature = 'example:demo';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '示例脚本';


    /**
     * 执行命令
     *
     */
    public function handle()
    {
        $x = Bus::sale()->saleComponent
            ->setIds([2041389178,2042469055])
            ->setFields(['id', 'uri', 'userinfoId', 'title'])
            ->get();
        var_dump($x);
        $this->info("process success");
    }

}