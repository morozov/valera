<?php

namespace Valera\Parser;

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
     * Constructor
     *
     * @param string[] $namespaces
     */
    public function __construct(array $namespaces = array())
    {
        foreach ($namespaces as $namespace) {
            $this->registerNamespace($namespace);
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
            throw new \InvalidArgumentException(
                'Parser class is loaded but does not implement ParserInterface'
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
