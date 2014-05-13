<?php

namespace Valera\Tests\Value;

use Valera\Blob;
use Valera\Content;
use Valera\Resource;
use Valera\Source;

/**
 * Unit test helper for value objects
 */
class Helper
{
    public static function getResource()
    {
        return new Resource(
            'http://example.com/',
            'http://example.org/',
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

    public static function getSource()
    {
        return new Source('index', self::getResource());
    }

    public static function getAnotherSource()
    {
        return new Source('default', self::getAnotherResource());
    }

    public static function getContent()
    {
        return new Content('<p>Lorem ipsum</p>', 'text/html; charset=utf-8', self::getSource());
    }

    public static function getAnotherContent()
    {
        return new Content('Hello world!', null, self::getAnotherSource());
    }

    public static function getDocument()
    {
        return array(
            'level1' => 'value1',
            'level2' => array(
                'level21' => 'value21',
                'image1' => self::getResource(),
                'level3' => array(
                    'level31' => 'value31',
                    'image2' => self::getBlob(),
                ),
            ),
        );
    }
}
