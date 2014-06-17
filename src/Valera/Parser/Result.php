<?php

namespace Valera\Parser;

use Valera\Resource;
use Valera\Worker\Result as BaseResult;

/**
 * Text parsing result
 */
class Result extends BaseResult
{
    /**
     * New documents
     *
     * @var array
     */
    protected $newDocuments = array();

    /**
     * Updated documents 
     *
     * @var array
     */
    protected $updatedDocuments = array();

    /**
     * New blobs
     *
     * @var array
     */
    protected $blobs = array();

    /**
     * New content references
     *
     * @var array
     */
    protected $references = array();

    /**
     * Returns new documents
     *
     * @return array
     */
    public function getNewDocuments()
    {
        $this->ensureSuccess();

        return $this->newDocuments;
    }

    /**
     * Adds new document
     *
     * @param mixed $id
     * @param array $data
     *
     * @return static
     * @throws \LogicException
     */
    public function addDocument($id, array $data)
    {
        $this->resolve();
        if (isset($this->newDocuments[$id])) {
            throw new \LogicException('Document #' . $id . ' has already been added');
        }

        $this->newDocuments[$id] = $data;

        return $this;
    }

    /**
     * Returns updated documents 
     *
     * @return array
     */
    public function getUpdatedDocuments()
    {
        $this->ensureSuccess();

        return $this->updatedDocuments;
    }

    /**
     * Updates document
     *
     * @param mixed $id
     * @param callable $callback
     *
     * @return static
     * @throws \LogicException
     */
    public function updateDocument($id, callable $callback)
    {
        $this->resolve();
        if (isset($this->updatedDocuments[$id])) {
            throw new \LogicException('Document #' . $id . ' has already been scheduled for update');
        }

        $this->updatedDocuments[$id] = $callback;

        return $this;
    }

    /**
     * Returns new blobs
     *
     * @return array
     */
    public function getBlobs()
    {
        $this->ensureSuccess();

        return $this->blobs;
    }

    /**
     * Adds downloaded blob
     *
     * @param \Valera\Resource $resource
     * @param string           $contents
     *
     * @return static
     * @throws \LogicException
     */
    public function addBlob(Resource $resource, $contents)
    {
        $this->resolve();
        $this->blobs[] = array($resource, $contents);

        return $this;
    }

    /**
     * Returns new content references
     *
     * @return array
     */
    public function getReferences()
    {
        $this->ensureSuccess();

        return $this->references;
    }

    /**
     * Adds new content source
     *
     * @param string $type
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $payload
     *
     * @return $this
     * @throws \LogicException
     */
    public function addReference(
        $type,
        $url,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        $payload = null
    ) {
        $this->resolve();

        $this->references[] = array(
            'type' => $type,
            'url' => $url,
            'method' => $method,
            'headers' => $headers,
            'payload' => $payload,
        );

        return $this;
    }
}
