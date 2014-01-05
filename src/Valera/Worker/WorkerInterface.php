<?php
namespace Valera\Worker;

interface WorkerInterface
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
     * @param \Valera\Resource $resource
     * @return mixed
     */
    public function addResource(Resource $resource);

    /**
     * @return mixed
     */
    public function run();
}
