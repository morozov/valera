<?php

namespace Valera\Parser\PostProcessor;

use Valera\Entity\Document;
use Valera\Parser\PostProcessor;

/**
 * Trims all strings
 */
class Trim implements PostProcessor
{
    /**
     * {@inheritDoc}
     */
    public function process(Document $document)
    {
        $document->iterate(function ($value) {
            return is_string($value);
        }, function (&$value) {
            $value = trim($value);
        });
    }
}
