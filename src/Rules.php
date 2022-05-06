<?php
declare(strict_types=1);

namespace Yng\Validator;

use Yng\Validator\Exceptions\ValidateException;

/**
 * @class   Rules
 * @author  Yng
 * @date    2022/04/23
 * @time    19:08
 * @package Yng\Validator
 */
class Rules
{
    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param           $key
     * @param           $value
     *
     * @return void
     * @throws ValidateException
     */
    public function required($key, $value)
    {
        if (\is_null($value)) {
            return $this->fail($message = $this->validator->getMessage($key . '.' . __FUNCTION__, $key . '字段是必须的'));
        }
        return true;
    }

    /**
     * @param           $key
     * @param           $value
     * @param           $max
     *
     * @return void
     * @throws ValidateException
     */
    public function max($key, $value, $max)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\mb_strlen((string)$value, 'utf8') > (int)$max) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '的长度最大' . $max));
        }
        return true;
    }

    /**
     * @param           $key
     * @param           $value
     * @param           $min
     *
     * @return void
     * @throws ValidateException
     */
    public function min($key, $value, $min)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\mb_strlen((string)$value, 'utf8') < (int)$min) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '的长度最小' . $min));
        }

        return true;
    }

    /**
     * @param           $key
     * @param           $value
     * @param int       $min
     * @param int       $max
     *
     * @return void
     * @throws ValidateException
     */
    public function length($key, $value, $min, $max)
    {
        if (\is_null($value)) {
            return false;
        }
        $min = (int)$min;
        $max = (int)$max;
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }
        $length = \mb_strlen((string)$value, 'utf8');
        if ($length <= $min || $length >= $max) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '的取值范围为' . $min . '-' . $max));
        }
        return true;
    }

    /**
     * @param           $key
     * @param           $value
     *
     * @return void
     * @throws ValidateException
     */
    public function bool($key, $value)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\is_bool($value) || \in_array(\strtolower($value, ['on', 'yes', 'true', '1', 'off', 'no', 'false', '0']))) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '必须是布尔类型'));
    }

    /**
     * @param           $key
     * @param           $value
     * @param           ...$in
     *
     * @return void
     * @throws ValidateException
     */
    public function in($key, $value, ...$in)
    {
        if (\is_null($value)) {
            return false;
        }
        if (in_array($value, $in)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '必须在' . implode(',', $in) . '范围内'));
    }

    /**
     * @param           $key
     * @param           $value
     * @param           $regex
     *
     * @return void
     * @throws ValidateException
     */
    public function regex($key, $value, $regex)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\preg_match($regex, $value, $match) && $match[0] == $value) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '的正则验证没有通过'));
    }

    /**
     * @param           $key
     * @param           $value
     * @param           $confirm
     *
     * @return void
     * @throws ValidateException
     */
    public function confirm($key, $value, $confirm)
    {
        if ($value != $this->validator->getData($confirm)) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '确认字段' . $confirm . '不一致'));
        }

        return true;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     * @throws ValidateException
     */
    public function integer($key, $value)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\is_int($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '必须由整数字符组成'));
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     * @throws ValidateException
     */
    public function numeric($key, $value)
    {
        if (\is_null($value)) {
            return false;
        }
        if (\is_numeric($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '必须是整数'));
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     * @throws ValidateException
     */
    public function array($key, $value)
    {
        if (\is_null($value)) {
            return false;
        }
        if (is_array($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '必须是数组'));
    }

    /**
     * @param $key
     * @param $value
     * @param ...$params
     *
     * @return void
     * @throws ValidateException
     */
    public function isset($key, $value, ...$params)
    {
        if ($this->array($key, $value)) {
            foreach ($params as $v) {
                if (!isset($value[$v])) {
                    return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, '需要的字段' . $v . '不存在'));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return false|void
     * @throws ValidateException
     */
    public function email($key, $value)
    {
        if (!is_null($value)) {
            return false;
        }
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . '的Email不合法'));
        }
    }

    /**
     * 验证失败
     *
     * @param $message
     *
     * @return false
     * @throws ValidateException
     */
    protected function fail($message)
    {
        if ($this->validator->isThrowable()) {
            throw new ValidateException($message);
        }
        $this->validator->errors()->push($message);
        return false;
    }
}
