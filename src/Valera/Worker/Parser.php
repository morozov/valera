<?php
namespace Valera\Worker;

use Valera\Content;
use Valera\Resource;
use Valera\Worker\ParserFactoryInterface;

/**
 * Class Parser
 * @package Valera\Worker
 */
class Parser implements WorkerInterface
{
    /**
     * @var
     */
    protected $successCallback;

    /**
     * @var
     */
    protected $failureCallback;

    /**
     * @var
     */
    protected $completeCallback;

    /**
     * @var
     */
    protected $factory;

    /**
     * @param $factory
     */
    public function __construct(ParserFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @var array
     */
    protected $contents = array();

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
     * @param $content
     * @return mixed
     */
    public function addJob($content)
    {

        $this->contents[] = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        foreach ($this->contents as $content) {
            if ($result = $this->parse($content)) {
                if (is_callable($this->successCallback)) {
                    $callback = $this->successCallback;
                    $callback($result);
                }
            } else {
                if (is_callable($this->failureCallback)) {
                    $callback = $this->failureCallback;
                    $callback();
                }
            }
            if (is_callable($this->completeCallback)) {
                $completeCallback = $this->completeCallback;
                $completeCallback($result);
            }
        }
    }

    /**
     * @param Content $content
     */
    protected function parse(Content $content)
    {
        $parser = $this->factory->getParser($content->getType());
        return $parser($content->getContent());
    }

    public function addResource(Resource $resource)
    {
    }
}
