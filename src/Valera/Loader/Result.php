<?php

namespace Valera\Loader;

use Valera\Result as BaseResult;

/**
 * Resource loading result
 */
class Result extends BaseResult
{
    protected $content;

    /**
     * Returns downloaded content
     * 
     * @return string
     */
    public function getContent()
    {
        $this->ensureSuccess();

        return $this->content;
    }

    /**
     * Sets downloaded content
     * 
     * @param string $content
     *
     * @return static
     * @throws \LogicException
     */
    public function setContent($content)
    {
        $this->resolve();
        if ($this->content !== null) {
            throw new \LogicException('Content has already been set');
        }

        $this->content = $content;

        return $this;
    }
}
