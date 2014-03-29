<?php

namespace Valera\Result;

class Success implements ResultInterface
{
    /**
     * Parsed data
     *
     * @var mixed
     */
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function accept(Visitor $visitor)
    {
        return $visitor->visitSuccess($this);
    }
}
