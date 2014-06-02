<?php

namespace Valera\Storage;

use Valera\Entity\Document;
use Valera\Resource;

interface DocumentStorage extends \Countable, \IteratorAggregate
{
    /**
     * Creates new document in storage
     *
     * @param \Valera\Entity\Document $document
     */
    public function create(Document $document);

    /**
     * Retrieves stored document or NULL if document not found
     *
     * @param string $id
     *
     * @return \Valera\Entity\Document|null
     */
    public function retrieve($id);

    /**
     * Finds document by embedded resource
     *
     * @param \Valera\Resource $resource
     *
     * @return \Iterator|\Valera\Entity\Document[]
     */
    public function findByResource(Resource $resource);

    /**
     * Updates existing document in storage
     *
     * @param \Valera\Entity\Document $document
     */
    public function update(Document $document);

    /**
     * Deletes document from storage
     *
     * @param string $id
     */
    public function delete($id);

    /**
     * Deletes all documents from storage
     */
    public function clean();
}
