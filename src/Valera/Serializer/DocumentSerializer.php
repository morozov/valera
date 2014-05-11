<?php

namespace Valera\Serializer;

use Valera\Blob;
use Valera\DocumentIterator;
use Valera\Resource;

/**
 * Document (not a value object yet) serializer
 */
class DocumentSerializer implements SerializerInterface
{
    /**
     * @var DocumentIterator
     */
    protected $documentIterator;

    /**
     * @var ResourceSerializer
     */
    protected $resourceSerializer;

    /**
     * @var BlobSerializer
     */
    protected $blobSerializer;

    /**
     * Constructor
     *
     * @param DocumentIterator   $documentIterator
     * @param ResourceSerializer $resourceSerializer
     * @param BlobSerializer     $blobSerializer
     */
    public function __construct(
        DocumentIterator $documentIterator,
        ResourceSerializer $resourceSerializer,
        BlobSerializer $blobSerializer
    ) {
        $this->documentIterator = $documentIterator;
        $this->resourceSerializer = $resourceSerializer;
        $this->blobSerializer = $blobSerializer;
    }

    /**
     * Creates array representation of document (not a value object yet)
     *
     * @param array $document
     *
     * @return array
     */
    public function serialize($document)
    {
        $this->documentIterator->iterate($document, function ($value) {
            return is_object($value);
        }, function (&$value) {
            if ($value instanceof Resource) {
                $type = 'resource';
                $serialized = $this->resourceSerializer->serialize($value);
            } elseif ($value instanceof Blob) {
                $type = 'blob';
                $serialized = $this->blobSerializer->serialize($value);
            } else {
                throw new \UnexpectedValueException(
                    'Unable to serialize embedded ' . get_class($value)
                );
            }

            $value = array_merge(array(
                '_type' => $type,
            ), $serialized);
        });

        return $document;
    }

    /**
     * Restores document (not a value object yet) from array representation
     *
     * @param array $params
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public function unserialize(array $params)
    {
        $document = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists('_type', $value)) {
                    $type = $value['_type'];
                    if ($type === 'resource') {
                        $serializer = $this->resourceSerializer;
                    } elseif ($type === 'blob') {
                        $serializer = $this->blobSerializer;
                    } else {
                        throw new \UnexpectedValueException(
                            'Unable to unserialize embedded ' . $type
                        );
                    }

                    $value = $serializer->unserialize($value);
                } else {
                    $value = $this->unserialize($value);
                }
            }

            $document[$key] = $value;
        }

        return $document;
    }
}
