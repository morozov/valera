<?php

namespace Valera\Tests\Serializer;

use Valera\Resource;
use Valera\Serializer\BlobSerializer;
use Valera\Serializer\ContentSerializer;
use Valera\Serializer\DocumentSerializer;
use Valera\Serializer\ResourceSerializer;
use Valera\Serializer\SourceSerializer;

class Helper
{
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

    public static function getAnotherSerializedResource()
    {
        return array(
            'url' => 'http://example.net/',
        );
    }

    public static function getSerializedBlob()
    {
        return array(
            'path' => '/path/to/blob',
            'resource' => self::getSerializedResource(),
        );
    }

    public static function getAnotherSerializedBlob()
    {
        return array(
            'path' => '/path/to/another/blob',
        );
    }

    public static function getSerializedBlobSource()
    {
        return array(
            'resource' => self::getSerializedResource(),
        );
    }

    public static function getSerializedDocumentSource()
    {
        return array(
            'type' => 'index',
            'resource' => self::getSerializedResource(),
        );
    }

    public static function getAnotherSerializedDocumentSource()
    {
        return array(
            'type' => 'default',
            'resource' => self::getAnotherSerializedResource(),
        );
    }

    public static function getSerializedDocument()
    {
        return array(
            'id' => 'test-document',
            'data' => array(
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
            ),
        );
    }

    public static function getSerializedContent()
    {
        return array(
            'content' => '<p>Lorem ipsum</p>',
            'mime_type' => 'text/html; charset=utf-8',
            'source' => self::getSerializedDocumentSource(),
        );
    }

    public static function getAnotherSerializedContent()
    {
        return array(
            'content' => 'Hello world!',
            'source' => self::getAnotherSerializedDocumentSource(),
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
            self::getResourceSerializer(),
            self::getBlobSerializer()
        );
    }
}
