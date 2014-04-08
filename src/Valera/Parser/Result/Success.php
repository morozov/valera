<?php

namespace Valera\Parser\Result;

use Valera\Resource;
use Valera\Result\Success as BaseSuccess;

class Success extends BaseSuccess
{
    /**
     * Collected documents
     *
     * @var array
     */
    protected $documents = array();

    /**
     * Collected blobs
     *
     * @var array
     */
    protected $blobs = array();

    /**
     * Additional resources to be parsed
     *
     * @var \Valera\Resource[]
     */
    protected $resources = array();

    public function addDocument($id, array $data)
    {
        $this->documents[$id] = $data;
    }

    public function addBlob($documentId, Resource $referrer, $contents)
    {
        $this->blobs[$documentId][$referrer->getHash()] = $contents;
    }

    public function addResource(
        $url,
        Resource $referrer,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        $this->resources[] = new Resource($url, $referrer, $method, $headers, $data);
    }

    /**
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    /**
     * @return array
     */
    public function getBlobs()
    {
        return $this->blobs;
    }

    /**
     * @return \Valera\Resource[]
     */
    public function getResources()
    {
        return $this->resources;
    }
}
