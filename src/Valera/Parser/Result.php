<?php

namespace Valera\Parser;

use Valera\Resource;
use Valera\Result as BaseResult;

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
     * New content sources
     *
     * @var array
     */
    protected $sources = array();

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
     * @param string $contents
     *
     * @return static
     * @throws \LogicException
     */
    public function addBlob($contents)
    {
        $this->resolve();
        $this->blobs[] = $contents;

        return $this;
    }

    /**
     * Returns new content sources
     *
     * @return array
     */
    public function getSources()
    {
        $this->ensureSuccess();

        return $this->sources;
    }

    /**
     * Adds new content source
     *
     * @param string $type
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $data
     *
     * @return $this
     * @throws \LogicException
     */
    public function addSource(
        $type,
        $url,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        $data = null
    ) {
        $this->resolve();
        $resource = new Resource($url, null, $method, $headers, $data);
        $hash = $resource->getHash();

        $this->sources[$hash] = array(
            'type' => $type,
            'url' => $url,
            'method' => $method,
            'headers' => $headers,
            'data' => $data,
        );

        return $this;
    }
}
