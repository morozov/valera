<?php

namespace Valera;

use Serializable;

/**
 * Class Resource
 * @package Valera
 */
class Resource implements Serializable
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    private $url;
    private $method;
    private $headers;
    private $data;
    private $hash;

    /**
     * @param $url string URL of the resource
     * @param string $method HTTP method to fetch resource
     * @param array $headers
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $url,
        $method = self::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        if (!is_string($url)) {
            throw new \InvalidArgumentException(
                sprintf('URL should be a string, %s given', gettype($url))
            );
        }
        if ($filteredUrl = filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $filteredUrl;
        } else {
            throw new \InvalidArgumentException(
                'Provided URL is incorrect according to » http://www.faqs.org/rfcs/rfc2396'
            );
        }

        if (!is_string($method)) {
            throw new \InvalidArgumentException(
                sprintf('HTTP method should be a string, %s given', gettype($url))
            );
        }
        $normalizedMethod = strtoupper($method);
        if (!in_array($normalizedMethod, array(self::METHOD_GET, self::METHOD_POST))) {
            throw new \InvalidArgumentException(
                sprintf('HTTP method expected to be %s or %s', self::METHOD_GET, self::METHOD_POST)
            );
        }
        $this->method = $normalizedMethod;
        foreach ($headers as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                throw new \InvalidArgumentException(
                    'All keys and values of the headers array must be of string type'
                );
            }
        }
        $this->headers = $headers;
        $this->data = $data;

        $this->hash();
    }

    /**
     * Returns resource URL
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns HTTP method that should be used to fetch this resource
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns array of headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $params = array(
            'url' => $this->getUrl(),
        );

        $method = $this->getMethod();
        if ($method != self::METHOD_GET) {
            $params['method'] = $method;
        }

        $headers = $this->getHeaders();
        if ($headers) {
            $params['headers'] = $headers;
        }

        $data = $this->getData();
        if ($data) {
            $params['data'] = $data;
        }

        return serialize($params);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $params = unserialize($serialized);
        $this->url = $params['url'];

        if (isset($params['method'])) {
            $this->method = $params['method'];
        } else {
            $this->method = self::METHOD_GET;
        }

        if (isset($params['headers'])) {
            $this->headers = $params['headers'];
        }

        if (isset($params['data'])) {
            $this->data = $params['data'];
        }

        $this->hash();
    }

    /**
     * Returns true if given resource is equal to current one
     * @param \Valera\Resource $resource
     * @return bool
     */
    public function equals(Resource $resource)
    {
        return $resource->getUrl() === $this->getUrl()
            && $resource->getMethod() === $this->getMethod()
            && $resource->getHeaders() == $this->getHeaders();
    }

    /**
     * Returns resource hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Hashes resource
     */
    protected function hash()
    {
        $this->hash = md5(serialize($this));
    }
}
