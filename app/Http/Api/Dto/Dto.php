<?php
/**
 *
 * @auther heyu 2020/7/9
 */

namespace App\Http\Api\Dto;

use App\Exceptions\ValidateException;
use App\Library\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use JsonSerializable;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

abstract class Dto implements JsonSerializable
{
    use ProvidesConvenienceMethods;

    protected $data = [];

    /**
     * 数据验证规则
     * @return array
     */
    abstract public static function getRules();

    /**
     * 数据验证提示
     * @return array
     */
    abstract public static function getErrorMessage();

    /**
     * 自定义验证字段名
     * @return array
     */
    abstract public static function getAttributes();

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
        return $this->data;
    }

    public function toTableAttribute()
    {
        return Util::unCamelizeArrayKey($this->data);
    }

    /**
     * 从请求中创建 DTO 对象
     *
     * @param Request $request
     * @return static
     * @throws ValidateException
     */
    public static function createFromRequest(Request $request)
    {
        $self = new static($request->all());

        $self->validate();
        $self->validateCustom();

        return $self;
    }

    /**
     * 自定义验证规则
     */
    protected function validateCustom()
    {
    }

    /**
     * 验证数据
     *
     * @return void
     * @throws ValidateException
     */
    protected function validate()
    {
        $validator = $this->getValidationFactory()
            ->make($this->data, static::getRules(), static::getErrorMessage(), static::getAttributes());

        if ($validator->fails()) {
            throw new ValidateException($validator->errors()->first());
        }
    }

    public function jsonSerialize()
    {
        return $this->data;
    }


    /**
     * 检查dto成员变量并追加至目标数组的指定字段(成员变量存在则追加)
     * @param $property
     * @param $arrKey
     * @param $data
     * @param string $default 默认值
     * @param string $fileType
     * @return array
     * @throws ValidateException
     */
    protected function appendData($property, $arrKey, $data, $fileType = "string", $default = null)
    {
        if (!isset($this->$property)) {
            if ($default === null) {
                return $data;
            }

            // 成员变量不存在且有默认值，则设置成员变量默认值
            $this->$property = $default;
        }

        $value = self::typeCast($fileType, $this->$property);
        array_set($data, $arrKey, $value);
        return $data;
    }

    /**
     * 检查dto成员变量并追加至目标数组的指定字段(成员变量存在且非空值则追加)
     * @param $property
     * @param $arrKey
     * @param $data
     * @param string $fileType
     * @param string $default 默认值
     * @return array
     * @throws ValidateException
     */
    protected function appendNotEmptyData($property, $arrKey, $data, $fileType = "string", $default = null)
    {
        if (!isset($this->$property)) {
            if ($default === null) {
                return $data;
            }

            // 成员变量不存在且有默认值，则设置成员变量默认值
            $this->$property = $default;
        }

        if ($this->$property === "") {
            return $data;
        }
        $value = self::typeCast($fileType, $this->$property);
        array_set($data, $arrKey, $value);
        return $data;
    }

    /**
     * 强制转换类型
     * @param $fileType
     * @param $value
     * @return bool|int|string
     * @throws ValidateException
     */
    protected static function typeCast($fileType, $value)
    {
        switch ($fileType) {
            case "string":
                return (string)$value;
            case "int":
                return (int)$value;
            case "bool":
                return (bool)$value;
            case "timestamp":
                return empty($value) ? 0 : (int)Carbon::parse($value)->timestamp;
            default:
                throw new ValidateException("非法类型{$fileType}");
        }
    }
}
