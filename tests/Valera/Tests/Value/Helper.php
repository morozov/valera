<?php

namespace Valera\Tests\Value;

use Valera\Blob;
use Valera\Content;
use Valera\Entity\Document;
use Valera\Resource;
use Valera\Source\BlobSource;
use Valera\Source\DocumentSource;
use Valera\Value\Reference;

/**
 * Unit test helper for value objects
 */
class Helper
{
    public static function getReference()
    {
        return new Reference(
            '/path',
            Resource::METHOD_POST,
            array('content-type' => 'application/json'),
            array('foo' => 'bar')
        );
    }

    public static function getReferrer()
    {
        return 'http://example.com/';
    }

    public static function getResource()
    {
        return new Resource(
            'http://example.com/path',
            'http://example.com/',
            Resource::METHOD_POST,
            array('content-type' => 'application/json'),
            array('foo' => 'bar')
        );
    }

    public static function getAnotherResource()
    {
        return new Resource(
            'http://example.net/'
        );
    }

    public static function getBlob()
    {
        return new Blob('/path/to/blob', self::getResource());
    }

    public static function getAnotherBlob()
    {
        return new Blob('/path/to/another/blob');
    }

    public static function getBlobSource()
    {
        return new BlobSource(self::getResource());
    }

    public static function getDocumentSource()
    {
        return new DocumentSource('index', self::getResource());
    }

    public static function getAnotherDocumentSource()
    {
        return new DocumentSource('default', self::getAnotherResource());
    }

    public static function getContent()
    {
        return new Content('<p>Lorem ipsum</p>', 'text/html; charset=utf-8', self::getDocumentSource());
    }

    public static function getAnotherContent()
    {
        return new Content('Hello world!', null, self::getAnotherDocumentSource());
    }

    public static function getDocument()
    {
        return new Document('test-document', array(
            'level1' => 'value1',
            'level2' => array(
                'level21' => 'value21',
                'image1' => self::getResource(),
                'level3' => array(
                    'level31' => 'value31',
                    'image2' => self::getBlob(),
                ),
            ),
        ));
    }

    public static function getAnotherDocument()
    {
        return new Document('another-document', array(
            'foo' => 'bar',
        ));
    }
}
