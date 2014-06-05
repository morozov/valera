<?php

namespace Valera\Source;

use Assert\Assertion;
use Valera\Resource;
use Valera\Source;

/**
 * Document source. Contains origin resource and parser type
 */
final class DocumentSource extends Source
{
    /**
     * The type of parser which should be used for source processing
     *
     * @var string
     */
    private $type;

    /**
     * Constructor
     *
     * @param string $type               Source type, the name of the parser that
     *                                   should be applied to parse its contents
     * @param \Valera\Resource $resource The HTTP resource representing the source
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct($type, Resource $resource)
    {
        Assertion::string($type);
        $this->type = $type;

        parent::__construct($resource);
    }

    /**
     * Returns the type of the source
     */
    public function getType()
    {
        return $this->type;
    }
}
