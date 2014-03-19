<?php

namespace Valera\Parser;

class Factory implements FactoryInterface
{
    protected $parsers = array();
    protected $namespaces = array();

    /** {@inheritDoc} */
    public function getParser($type)
    {
        if (isset($this->parsers[$type])) {
            return $this->parsers[$type];
        }

        $parser = $this->loadParser($type);
        if ($parser) {
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
        foreach ($this->namespaces as $namespace) {
            $class = $namespace . '\\' . $type;
            if (class_exists($class)) {
                return new $class;
            }
        }

        return null;
    }
}
