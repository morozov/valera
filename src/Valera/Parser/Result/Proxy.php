<?php

namespace Valera\Parser\Result;

use Valera\Result\Proxy as BaseProxy;

class Proxy extends BaseProxy
{
    /**
     * @param mixed $data
     *
     * @return Success
     */
    protected function getSuccess($data)
    {
        return new Success($data);
    }
}
