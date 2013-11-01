<?php

namespace Valera\Fetch;
use Valera\Resource;

class CurlClient implements Worker
{
    protected $getResources = array();

    protected $postResources = array();

    protected $successCallback;

    protected $failureCallback;

    protected $completeCallback;

    /**
     * @param callable $callback
     * @return mixed
     */
    public function setSuccessCallback(callable $callback)
    {
        $this->successCallback = $callback;
        return $this;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function setFailureCallback(callable $callback)
    {
        $this->failureCallback = $callback;
        return $this;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function setCompleteCallback(callable $callback)
    {
        $this->completeCallback = $callback;
        return $this;
    }

    /**
     * @param \Valera\Resource $resource
     * @return mixed
     */
    public function addResource(Resource $resource)
    {
        if ($resource->getMethod === Resource::METHOD_GET) {
            $this->getResources[$resource->getUrl()] = $resource;
        } else {
            $this->postResources[$resource->getUrl()] = $resource;
        }

    }

    /**
     * @return mixed
     */
    public function fetch()
    {

    }
}
