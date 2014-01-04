<?php

namespace Valera\Fetch;
use Valera\Resource;

abstract class HttpClient  implements Worker
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
    public function addJob($resource)
    {
        if (Resource::METHOD_GET === $resource->getMethod()) {
            $this->getResources[$resource->getUrl()] = $resource;
        } else {
            $this->postResources[$resource->getUrl()] = $resource;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function run();

}
