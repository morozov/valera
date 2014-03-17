<?php

namespace Valera;

interface Queueable
{
    /**
     * Returns queueable item hash
     *
     * @return string
     */
    public function getHash();
}
