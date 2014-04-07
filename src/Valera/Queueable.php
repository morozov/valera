<?php

namespace Valera;

use Valera\Serialize\Serializable;

interface Queueable extends Serializable
{
    /**
     * Returns queueable item hash
     *
     * @return string
     */
    public function getHash();
}
