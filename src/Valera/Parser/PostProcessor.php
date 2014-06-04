<?php

namespace Valera\Parser;

use Valera\Entity\Document;

/**
 * Post processor of created/updated document
 */
interface PostProcessor
{
    /**
     * Processes document data
     *
     * @param Document $document
     */
    public function process(Document $document);
}
