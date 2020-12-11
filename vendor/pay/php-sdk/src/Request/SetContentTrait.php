<?php

namespace PayCenter\Request;

trait SetContentTrait
{
    /**
     * 设置业务内容参数对象/数组
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->contentJson = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * 设置业务内容参数 JSON
     * @param string $contentJson
     * @return $this
     */
    public function setContentJson(string $contentJson)
    {
        $this->contentJson = $contentJson;
        return $this;
    }
}
