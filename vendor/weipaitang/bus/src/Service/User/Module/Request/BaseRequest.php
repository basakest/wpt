<?php

namespace WptBus\Service\User\Module\Request;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class BaseRequest implements Arrayable, Jsonable, JsonSerializable
{
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}