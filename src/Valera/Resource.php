<?php

namespace Valera;

use Valera\Serialize\Serializable;
use Valera\Serialize\Serializer;

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

    public static function fromArray(array $params)
    {
        if (!isset($params['url'])) {
            throw new \Exception('efwef');
        }
        $url = $params['url'];

        if (isset($params['method'])) {
            $method = $params['method'];
        } else {
            $method = self::METHOD_GET;
        }

        if (isset($params['headers'])) {
            $headers = $params['headers'];
        } else {
            $headers = array();
        }

        if (isset($params['data'])) {
            $data = $params['data'];
        } else {
            $data = array();
        }

        return new self($url, $method, $headers, $data);
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

    public function accept(Serializer $serializer)
    {
        return $serializer->serializeResource($this);
    }
}
