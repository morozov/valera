<?php

namespace Valera\Storage\DocumentStorage;

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
            'blobs' => 1,
        ));
    }

    public function create($id, array $data, array $blobs)
    {
        try {
            $this->db->documents->insert(array(
                '_id' => $id,
                'data' => $this->serialize($data),
                'blobs' => $blobs,
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
    public function findByBlob($hash)
    {
        $documents = $this->db->documents->find(array(
            'blobs' => $hash,
        ));

        $result = array();
        foreach ($documents as $document) {
            $result[$document['_id']] = $this->unserialize($document['data']);
        }

        return $result;
    }

    public function update($id, array $data, array $blobs)
    {
        $this->db->documents->update(array(
            '_id' => $id,
        ), array(
            '_id' => $id,
            'data' => $this->serialize($data),
            'blobs' => $blobs,
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
