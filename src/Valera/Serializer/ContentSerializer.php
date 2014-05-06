<?php

namespace Valera\Serializer;

use Valera\Content;

/**
 * Content value object serializer
 */
class ContentSerializer implements SerializerInterface
{
    /**
     * @var SourceSerializer
     */
    protected $sourceSerializer;

    /**
     * Constructor
     *
     * @param SourceSerializer $sourceSerializer
     */
    public function __construct(SourceSerializer $sourceSerializer)
    {
        $this->sourceSerializer = $sourceSerializer;
    }

    /**
     * Creates array representation of content value object
     *
     * @param Content $content
     *
     * @return array
     */
    public function serialize($content)
    {
        return array(
            'content' => $content->getContent(),
            'source' => $this->sourceSerializer->serialize($content->getSource()),
        );
    }

    /**
     * Restores content value object from array representation
     *
     * @param array $params
     *
     * @return Content
     * @throws \InvalidArgumentException
     */
    public function unserialize(array $params)
    {
        return new Content(
            $params['content'],
            $this->sourceSerializer->unserialize($params['source'])
        );
    }
}
