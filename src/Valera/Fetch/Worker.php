<?php
namespace Valera\Fetch;
use Valera\Resource;
use Valera\ResourceInterface;

interface Worker
{
    /**
     * @param callable $callback
     * @return mixed
     */
    public function setSuccessCallback(callable $callback);

    /**
     * @param callable $callback
     * @return mixed
     */
    public function setFailureCallback(callable $callback);

    /**
     * @param callable $callback
     * @return mixed
     */
    public function setCompleteCallback(callable $callback);

    /**
     * @param \Valera\Resource|\Valera\ResourceInterface $resource
     * @return mixed
     */
    public function addResource(ResourceInterface $resource);

    /**
     * @return mixed
     */
    public function fetch();
}
