<?php

namespace Valera\Parser;

use \UnexpectedValueException;

class Factory implements FactoryInterface
{
    protected $parsers = array();
    protected $namespaces = array();

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
            if (!$parser instanceof ParserInterface) {
                throw new UnexpectedValueException(
                    'Parser class is loaded but doesn\'t implement the'
                    . ' ParserInterface'
                );
            }
            $this->parsers[$type] = $parser;
        }

        return $parser;
    }

    public function registerNamespace($namespace)
    {
        $this->namespaces[$namespace] = true;
    }

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
    function camelize($str, $ucfirst = false) {
        if ($ucfirst) {
            $str[0] = strtoupper($str[0]);
        }
        return preg_replace_callback('/[_-]([a-z])/', function($c) {
            return strtoupper($c[1]);
        }, $str);
    }
}
