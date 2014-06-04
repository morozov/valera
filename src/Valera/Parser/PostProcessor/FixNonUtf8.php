<?php

namespace Valera\Parser\PostProcessor;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Entity\Document;
use Valera\Parser\PostProcessor;

/**
 * Makes sure all string in document data are valid UTF-8
 */
class FixNonUtf8 implements PostProcessor
{
    use LoggerAwareTrait;

    /**
     * Replacement string
     *
     * @var string
     */
    private $replacement;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $replacement
     */
    public function __construct(LoggerInterface $logger, $replacement = '?')
    {
        Assertion::string($replacement);

        $this->replacement = $replacement;
        $this->setLogger($logger);
    }

    /**
     * {@inheritDoc}
     */
    public function process(Document $document)
    {
        $document->iterate(function ($value) {
            return is_string($value);
        }, function (&$value, array $path) use ($document) {
            $filtered = $this->filter($value);
            if ($filtered !== $value) {
                $value = $filtered;
                $this->logger->warning(sprintf(
                    'The %s property of document #%s is not a valid UTF-8 string',
                    implode('.', $path),
                    $document->getId()
                ));
            }
        });
    }

    /**
     * Filters original string by replacing non-ut8 characters with replacement string
     *
     * @param string $value
     *
     * @return string
     * @link http://magp.ie/2011/01/06/remove-non-utf8-characters-from-string-with-php/
     */
    private function filter($value)
    {
        // reject overly long 2 byte sequences, as well as characters above U+10000
        $value = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'
            . '|[\x00-\x7F][\x80-\xBF]+'
            . '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'
            . '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'
            . '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            $this->replacement,
            $value
        );

        // reject overly long 3 byte sequences and UTF-16 surrogates
        $value = preg_replace(
            '/\xE0[\x80-\x9F][\x80-\xBF]'
            . '|\xED[\xA0-\xBF][\x80-\xBF]/S',
            $this->replacement,
            $value
        );

        return $value;
    }
}
