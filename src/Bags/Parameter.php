<?php

namespace Yng\Validator\Bags;

class Parameter
{
    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @param string $error
     *
     * @return $this
     */
    public function push(string $error)
    {
        $this->items[] = $error;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
}
