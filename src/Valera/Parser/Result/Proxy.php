<?php

namespace Valera\Parser\Result;

use Valera\Result\Proxy as BaseProxy;

class Proxy extends BaseProxy
{
    /**
     * @return Success
     */
    protected function getSuccess()
    {
        return new Success();
    }
}
