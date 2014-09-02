<?php

namespace Valera\Worker;

use Valera\Queueable;

interface Converter
{
    /**
     * @param \Valera\Queueable $item
     */
    public function convert(Queueable $item);
}
