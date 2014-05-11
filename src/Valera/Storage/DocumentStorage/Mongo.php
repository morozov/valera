<?php

namespace Valera\Storage\DocumentStorage;

use Valera\Resource;
use Valera\Serializer\DocumentSerializer;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class Mongo implements DocumentStorage
{
    /**
     * @var \MongoDB
     */
    protected $db;

    /**
     * @var DocumentSerializer
     */
    protected $serializer;

    /**
     * Constructor
     *
     * @param \MongoDB           $db
     * @param DocumentSerializer $serializer
     */
    public function __construct(\MongoDB $db, DocumentSerializer $serializer)
    {
        $this->db = $db;
        $this->serializer = $serializer;
        $this->db->documents->ensureIndex(array(
            'embedded' => 1,
        ));
    }

    public function create($id, array $data, array $resources)
    {
        try {
            $this->db->documents->insert(array(
                '_id' => $id,
                'data' => $this->serialize($data),
                'embedded' => $this->getHashes($resources),
            ));
        } catch (\MongoDuplicateKeyException $e) {
            throw new \DomainException('Document already exists', 0, $e);
        }
    }

    public function retrieve($id)
    {
        $document = $this->db->documents->findOne(array(
            '_id' => $id
        ));

        if ($document) {
            return $this->unserialize($document['data']);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findByResource(Resource $resource)
    {
        $cursor = $this->db->documents->find(array(
            'embedded' => $resource->getHash(),
        ));

        return $this->getCursorIterator($cursor);
    }

    public function update($id, array $data, array $resources)
    {
        $this->db->documents->update(array(
            '_id' => $id,
        ), array(
            '_id' => $id,
            'data' => $this->serialize($data),
            'embedded' => $this->getHashes($resources),
        ));
    }

    public function delete($id)
    {
        $this->db->documents->findAndModify(
            array(
                '_id' => $id,
            ),
            array(),
            null,
            array(
                'remove' => true,
            )
        );
    }

    protected function getHashes(array $resources)
    {
        return array_map(function (Resource $resource) {
            return $resource->getHash();
        }, $resources);
    }

    public function clean()
    {
        $this->db->documents->drop();
    }

    public function count()
    {
        return $this->db->documents->count();
    }

    public function getIterator()
    {
        $cursor = $this->db->documents->find();
        return $this->getCursorIterator($cursor);
    }

    /**
     * @param \MongoCursor $cursor
     *
     * @return Mongo\Iterator
     */
    protected function getCursorIterator($cursor)
    {
        return new Mongo\Iterator($cursor, $this->serializer);
    }

    /**
     * Serializes document before storing
     *
     * @param array $document
     *
     * @return array
     */
    protected function serialize(array $document)
    {
        return $this->serializer->serialize($document);
    }

    /**
     * Unserializes document after retrieving
     *
     * @param array $params
     *
     * @return array
     */
    protected function unserialize(array $params)
    {
        return $this->serializer->unserialize($params);
    }
}
