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
        $serialized = array(
            'content' => $content->getContent(),
            'source' => $this->sourceSerializer->serialize($content->getSource()),
        );

        $mimeType = $content->getMimeType();
        if ($mimeType !== null) {
            $serialized['mime_type'] = $mimeType;
        }

        return $serialized;
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
        if (isset($params['mime_type'])) {
            $mimeType = $params['mime_type'];
        } else {
            $mimeType = null;
        }

        return new Content(
            $params['content'],
            $mimeType,
            $this->sourceSerializer->unserialize($params['source'])
        );
    }
}
