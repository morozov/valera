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
    
    /**
     * Returns array representing object state
     *
     * @return array
     */
    public function toArray();
}
