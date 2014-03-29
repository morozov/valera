<?php

namespace Valera\Result;

use InvalidArgumentException;

class Failure implements ResultInterface
{
    private $message;

    public function __construct($message = null)
    {
        if ($message !== null && !is_string($message)) {
            throw new InvalidArgumentException(sprintf(
                'Message must be NULL or string, %s given',
                gettype($message)
            ));
        }

        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function accept(Visitor $visitor)
    {
        return $visitor->visitFailure($this);
    }
}
