<?php
/**
 *
 * @auther heyu 2020/7/22
 */

namespace App\Library;

class DataBean implements \JsonSerializable
{

    protected $data = [];

    protected $visible = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function toArray()
    {
        if (!empty($this->visible)) {
            $data = [];
            foreach ($this->data as $k => $v) {
                if (in_array($k, $this->visible)) {
                    $data[$k] = $v;
                }
            }
            return $data;
        } else {
            return $this->data;
        }
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }


    public function setVisible($fields = [])
    {
        $this->visible = $fields;
        return $this;
    }
}
