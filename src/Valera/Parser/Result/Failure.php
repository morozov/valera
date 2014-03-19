<?php

namespace Valera\Parser\Result;

use InvalidArgumentException;

class Failure implements ResultInterface
{
    private $message;

    public function __construct($message)
    {
        if (!is_string($message)) {
            throw new InvalidArgumentException(
                sprintf('Message must be a string, %s given', gettype($message))
            );
        }

        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
