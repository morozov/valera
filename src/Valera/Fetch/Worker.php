<?php
namespace Valera\Fetch;

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
     * @param $job
     * @return mixed
     */
    public function addJob($job);

    /**
     * @return mixed
     */
    public function run();
}
