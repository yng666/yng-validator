<?php
declare(strict_types=1);

namespace Yng\Validator;

use Yng\Validator\Bags\Errors;

/**
 * @class   Validator
 * @author  Yng
 * @date    2022/04/23
 * @time    19:08
 * @package Yng\Validator
 */
class Validator
{
    /**
     * @var Rules
     */
    protected Rules $rules;

    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var array
     */
    protected array $message = [];

    /**
     * @var array
     */
    protected array $valid = [];

    /**
     * @var Errors
     */
    protected Errors $errors;

    /**
     * @var bool
     */
    protected bool $throwable = false;

    /**
     * @return array|string|null
     */
    public function getData($key = null)
    {
        return $key ? ($this->data[$key] ?? null) : $this->data;
    }

    /**
     * @return string|null
     */
    public function getMessage($key, $default = '验证失败')
    {
        return $this->message[$key] ?? $default;
    }

    /**
     * @return bool
     */
    public function isThrowable(): bool
    {
        return $this->throwable;
    }

    /**
     * @param bool $throwable
     */
    public function setThrowable(bool $throwable)
    {
        $this->throwable = $throwable;

        return $this;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $message
     *
     * @return $this
     */
    public function make(array $data, array $rules, array $message = [])
    {
        $this->rules   = new Rules($this);
        $this->errors  = new Errors();
        $this->data    = $data;
        $this->message = $message;

        foreach ($rules as $key => $rule) {
            $value = $this->getData($key);
            if (!\is_array($rule)) {
                $rule = \explode('|', $rule);
            }
            foreach ($rule as $ruleItem) {
                $ruleItem   = \explode(':', $ruleItem, 2);
                $ruleName   = $ruleItem[0];
                $ruleParams = empty($ruleItem[1]) ? [] : \explode(',', $ruleItem[1]);
                if ($this->rules->$ruleName($key, $value, ...$ruleParams)) {
                    $this->valid[$key] = $value;
                }
            }
        }

        return $this;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function valid()
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->errors->isEmpty();
    }

    /**
     * @return array
     */
    public function failed()
    {
        return $this->errors->all();
    }
}
