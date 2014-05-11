<?php

namespace Valera\Parser;

use Valera\Parser\Factory\CallbackParser;

/**
 * Parser factory
 */
class Factory implements FactoryInterface
{
    /**
     * @var \Valera\Parser\ParserInterface[]
     */
    protected $parsers = array();

    /**
     * @var string[]
     */
    protected $namespaces = array();

    /**
     * @var \SplObjectStorage|AdapterInterface[]
     */
    protected $adapters = array();

    /**
     * Constructor
     *
     * @param string[]                          $namespaces
     * @param \Valera\Parser\AdapterInterface[] $adapters
     */
    public function __construct(array $namespaces = array(), array $adapters = array())
    {
        foreach ($namespaces as $namespace) {
            $this->registerNamespace($namespace);
        }

        $this->adapters = new \SplObjectStorage();
        foreach ($adapters as $adapter) {
            $this->registerAdapter($adapter);
        }
    }

    /** {@inheritDoc} */
    public function getParser($type)
    {
        if (isset($this->parsers[$type])) {
            return $this->parsers[$type];
        }

        $parser = $this->loadParser($type);
        if ($parser) {
            $parser = $this->registerParser($type, $parser);
        }

        return $parser;
    }

    /**
     * Registers parser
     *
     * @param string $type
     * @param mixed $parser
     *
     * @return ParserInterface
     * @throws \InvalidArgumentException
     */
    public function registerParser($type, $parser)
    {
        if (!$parser instanceof ParserInterface) {
            $parser = $this->wrap($parser);
        }

        if (!$parser instanceof ParserInterface) {
            throw new \InvalidArgumentException(
                'Parser class is loaded but does not implement'
                . ' ParserInterface and cannot be wrapped into adapter'
            );
        }

        $this->parsers[$type] = $parser;

        return $parser;
    }

    /**
     * Registers parser namespace
     *
     * @param string $namespace
     */
    public function registerNamespace($namespace)
    {
        $this->namespaces[$namespace] = true;
    }

    /**
     * Registers parser interface adapter
     *
     * @param AdapterInterface $adapter
     */
    public function registerAdapter(AdapterInterface $adapter)
    {
        $this->adapters->attach($adapter);
    }

    /**
     * Unregisters parser interface adapter
     *
     * @param AdapterInterface $adapter
     */
    public function unregisterAdapter(AdapterInterface $adapter)
    {
        $this->adapters->detach($adapter);
    }

    /**
     * Returns class of parse of the given type, or NULL if class is not found
     *
     * @param string $type
     *
     * @return string|null
     */
    protected function loadParser($type)
    {
        foreach (array_keys($this->namespaces) as $namespace) {
            $class = $namespace . '\\' . $this->camelize($type, true);
            if (class_exists($class)) {
                return new $class;
            }
        }

        return null;
    }

    /**
     * Wraps parser into ParserInterface, or returns NULL
     * if the needed wrapper is not found
     *
     * @param mixed $parser
     *
     * @return ParserInterface|null
     */
    protected function wrap($parser)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($parser)) {
                $callback = $adapter->wrap($parser);
                $wrapped = new CallbackParser($callback);
                return $wrapped;
            }
        }

        return null;
    }

    /**
     * Translates a string with underscores or dashes
     * into camel case (e.g. first_name -> firstName)
     *
     * @param string $str String in underscore format
     * @param bool $ucfirst If true, capitalise the first char in $str
     * @return string $str translated into camel caps
     */
    protected function camelize($str, $ucfirst = false)
    {
        if ($ucfirst) {
            $str[0] = strtoupper($str[0]);
        }
        return preg_replace_callback('/[_-]([a-z])/', function ($c) {
            return strtoupper($c[1]);
        }, $str);
    }
}
