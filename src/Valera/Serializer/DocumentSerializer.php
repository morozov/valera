<?php

namespace Valera\Serializer;

use Valera\Blob;
use Valera\Entity\Document;
use Valera\Resource;

/**
 * Document (not a value object yet) serializer
 */
class DocumentSerializer implements SerializerInterface
{
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
     * @param ResourceSerializer $resourceSerializer
     * @param BlobSerializer     $blobSerializer
     */
    public function __construct(
        ResourceSerializer $resourceSerializer,
        BlobSerializer $blobSerializer
    ) {
        $this->resourceSerializer = $resourceSerializer;
        $this->blobSerializer = $blobSerializer;
    }

    /**
     * Creates array representation of document (not a value object yet)
     *
     * @param \Valera\Entity\Document $document
     *
     * @return array
     */
    public function serialize($document)
    {
        $data = $document->getData();
        return array(
            'id' => $document->getId(),
            'data' => $this->serializeData($data),
        );
    }

    /**
     * Restores document from array representation
     *
     * @param array $params
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public function unserialize(array $params)
    {
        $data = $this->unserializeData($params['data']);
        return new Document($params['id'], $data);
    }

    /**
     * Recursively serializes document data
     *
     * @param array $data
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    private function serializeData(array $data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $serialized = $this->serializeData($value);
            } elseif (is_object($value)) {
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

                $serialized['_type'] = $type;
            } else {
                $serialized = $value;
            }

            $result[$key] = $serialized;
        }

        return $result;
    }

    /**
     * Recursively unserializes document data
     *
     * @param array $params
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    private function unserializeData(array $params)
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
                    $value = $this->unserializeData($value);
                }
            }

            $document[$key] = $value;
        }

        return $document;
    }
}
