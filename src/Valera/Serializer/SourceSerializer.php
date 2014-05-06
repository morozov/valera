<?php

namespace Valera\Serializer;

use Valera\Source;

/**
 * Source value object serializer
 */
class SourceSerializer implements SerializerInterface
{
    /**
     * @var ResourceSerializer
     */
    protected $resourceSerializer;

    /**
     * Constructor
     *
     * @param ResourceSerializer $resourceSerializer
     */
    public function __construct(ResourceSerializer $resourceSerializer)
    {
        $this->resourceSerializer = $resourceSerializer;
    }

    /**
     * Creates array representation of source value object
     *
     * @param Source $source
     *
     * @return array
     */
    public function serialize($source)
    {
        return array(
            'type' => $source->getType(),
            'resource' => $this->resourceSerializer->serialize($source->getResource()),
        );
    }

    /**
     * Restores source value object from array representation
     *
     * @param array $params
     *
     * @return Source
     * @throws \InvalidArgumentException
     */
    public function unserialize(array $params)
    {
        return new Source(
            $params['type'],
            $this->resourceSerializer->unserialize($params['resource'])
        );
    }
}
