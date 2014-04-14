<?php

namespace Valera\Parser\Result;

use Valera\Document;
use Valera\Resource;
use Valera\Source;
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
     * Additional sources to be parsed
     *
     * @var \Valera\Source[]
     */
    protected $sources = array();

    public function addDocument($id, array $data, $type = null)
    {
        $this->documents[] = new Document($id, $data, $type);
    }

    public function addSource(
        $type,
        $url,
        Resource $referrer,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        $resource = new Resource($url, $referrer, $method, $headers, $data);
        $source = new Source($type, $resource);
        $this->sources[] = $source;
    }

    /**
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return \Valera\Resource[]
     */
    public function getSources()
    {
        return $this->sources;
    }
}
