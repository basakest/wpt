<?php


namespace WptOrder\OrderService\Contracts;


interface Configurable
{
    public function getConfig(): array;

    public function setConfig(array $config = []);

}