<?php

namespace Valera\Storage\DocumentStorage;

use DomainException;
use MongoDuplicateKeyException;
use Valera\Blob;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class Mongo implements DocumentStorage
{
    public function __construct(\MongoDB $db)
    {
        $this->db = $db;
        $this->db->documents->ensureIndex(array(
            'blobs' => 1,
        ));
    }

    public function create($id, array $data, array $blobs)
    {
        try {
            $this->db->documents->insert(array(
                '_id' => $id,
                'data' => $data,
                'blobs' => $blobs,
            ));
        } catch (MongoDuplicateKeyException $e) {
            throw new DomainException('Document already exists', 0, $e);
        }
    }

    public function retrieve($id)
    {
        $document = $this->db->documents->findOne(array(
            '_id' => $id
        ));

        if ($document) {
            return $document['data'];
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
            $result[$document['_id']] = $document['data'];
        }

        return $result;
    }

    public function update($id, array $data, array $blobs)
    {
        $this->db->documents->update(array(
            '_id' => $id,
        ), array(
            '_id' => $id,
            'data' => $data,
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
}
