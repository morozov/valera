<?php

namespace Valera\Blob;

use Valera\Blob;

class Local implements Blob
{
    /**
     * @var string
     */
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function __toString()
    {
        return $this->getPath();
    }
}
