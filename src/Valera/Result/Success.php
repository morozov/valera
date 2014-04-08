<?php

namespace Valera\Result;

class Success implements ResultInterface
{
    public function accept(Visitor $visitor)
    {
        return $visitor->visitSuccess($this);
    }
}
