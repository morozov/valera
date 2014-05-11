<?php

namespace Valera\Tests\Serializer;

use Valera\Blob;
use Valera\Content;
use Valera\DocumentIterator;
use Valera\Resource;
use Valera\Serializer\BlobSerializer;
use Valera\Serializer\ContentSerializer;
use Valera\Serializer\DocumentSerializer;
use Valera\Serializer\ResourceSerializer;
use Valera\Serializer\SourceSerializer;
use Valera\Source;

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

    public static function getSource()
    {
        return new Source('index', self::getResource());
    }

    public static function getContent()
    {
        return new Content('Lorem ipsum', self::getSource());
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

    public static function getSerializedResource()
    {
        return array(
            'url' => 'http://example.com/',
            'referrer' => 'http://example.org/',
            'method' => Resource::METHOD_POST,
            'headers' => array('content-type' => 'application/json'),
            'data' => array('foo' => 'bar'),
        );
    }

    public static function getSerializedBlob()
    {
        return array(
            'path' => '/path/to/blob',
            'resource' => self::getSerializedResource(),
        );
    }

    public static function getSerializedSource()
    {
        return array(
            'type' => 'index',
            'resource' => self::getSerializedResource(),
        );
    }

    public static function getSerializedDocument()
    {
        return array(
            'level1' => 'value1',
            'level2' => array(
                'level21' => 'value21',
                'image1' => array_merge(array(
                    '_type' => 'resource'
                ), self::getSerializedResource()),
                'level3' => array(
                    'level31' => 'value31',
                    'image2' => array_merge(array(
                        '_type' => 'blob'
                    ), self::getSerializedBlob()),
                ),
            ),
        );
    }

    public static function getSerializedContent()
    {
        return array(
            'content' => 'Lorem ipsum',
            'source' => self::getSerializedSource(),
        );
    }

    public static function getResourceSerializer()
    {
        return new ResourceSerializer();
    }

    public static function getBlobSerializer()
    {
        return new BlobSerializer(self::getResourceSerializer());
    }

    public static function getSourceSerializer()
    {
        return new SourceSerializer(self::getResourceSerializer());
    }

    public static function getContentSerializer()
    {
        return new ContentSerializer(self::getSourceSerializer());
    }

    public static function getDocumentSerializer()
    {
        return new DocumentSerializer(
            new DocumentIterator(),
            self::getResourceSerializer(),
            self::getBlobSerializer()
        );
    }
}
