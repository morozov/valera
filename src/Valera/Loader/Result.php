<?php

namespace Valera\Loader;

use Valera\Worker\Result as BaseResult;

/**
 * Resource loading result
 */
class Result extends BaseResult
{
    /**
     * Textual content
     *
     * @var string
     */
    protected $content;

    /**
     * Content MIME type
     *
     * @var string|null
     */
    protected $mimeType;

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
     * Returns content MIME type
     *
     * @return string|null
     */
    public function getMimeType()
    {
        $this->ensureSuccess();

        return $this->mimeType;
    }

    /**
     * Sets downloaded content
     * 
     * @param string      $content
     * @param string|null $mimeType
     *
     * @return static
     * @throws \LogicException
     */
    public function setContent($content, $mimeType)
    {
        $this->resolve();
        if ($this->content !== null) {
            throw new \LogicException('Content has already been set');
        }

        $this->content = $content;
        $this->mimeType = $mimeType;

        return $this;
    }
}
