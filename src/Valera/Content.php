<?php

namespace Valera;

use Assert\Assertion;

/**
 * Downloaded content
 */
final class Content implements Queueable
{
    /**
     * String content
     *
     * @var string
     */
    private $content;

    /**
     * MIME type of the content
     *
     * @var string
     */
    private $mimeType;

    /**
     * Content source
     *
     * @var \Valera\Source\DocumentSource
     */
    private $source;

    /**
     * Constructor
     *
     * @param string      $content  The effective contents
     * @param string|null $mimeType MIME type of the content
     * @param Source      $source   Content source
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct($content, $mimeType, Source $source)
    {
        Assertion::string($content);
        Assertion::nullOrString($mimeType);

        $this->content = $content;
        $this->mimeType = $mimeType;
        $this->source = $source;
    }

    /**
     * Returns effective contents
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns content MIME type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Returns content source
     *
     * @return \Valera\Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns the type of the content source
     *
     * @return string
     */
    public function getType()
    {
        return $this->source->getType();
    }

    /**
     * Returns the origin resource of the content
     *
     * @return \Valera\Resource
     */
    public function getResource()
    {
        return $this->source->getResource();
    }

    /**
     * Returns content hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->source->getHash();
    }
}
