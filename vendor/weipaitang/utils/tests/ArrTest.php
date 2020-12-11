<?php


use WptUtils\Arr;

class ArrTest extends \PHPUnit\Framework\TestCase
{
    public function testArr()
    {


        // print_r(Arr::rand([1,2,3,4,5], 3, true));
        $arr = [
            (object)[
                'name' => '1',
                'sort' => 10,
            ],
            (object)[
                'name' => '2',
                'sort' => 11,
            ],
            (object)[
                'name' => '3',
                'sort' => 12,
            ],
        ];

        $marr = [
            [
                'name' => '1',
                'sort' => 10,
            ],
            [
                'name' => '2',
                'sort' => 13,
            ],
            [
                'name' => '3',
                'sort' => 12,
            ]
        ];

        $a = Arr::multiSort($marr, ['sort' => SORT_DESC, 'name' => SORT_ASC]);
        print_r($a);


        $out = Arr::sort($arr, 'sort', 'desc');
        print_r($out);

        die;
    }
}
