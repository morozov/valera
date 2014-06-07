<?php

namespace Valera;

use Assert\Assertion;

/**
 * Class Resource
 * @package Valera
 */
final class Resource
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    private $url;
    private $referrer;
    private $method;
    private $headers;
    private $data;
    private $hash;

    /**
     * @param $url string URL of the resource
     * @param string $referrer HTTP referer
     * @param string $method HTTP method to fetch resource
     * @param array $headers
     * @param array $data
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        $url,
        $referrer = null,
        $method = self::METHOD_GET,
        array $headers = array(),
        $data = null
    ) {
        if ($referrer === null) {
            // in case if referrer is not specified, the URL must be absolute
            Assertion::url($url);
        } else {
            // otherwise referrer itself must be absolute URL
            Assertion::url($referrer);
        }

        Assertion::string($method);
        $method = strtoupper($method);
        Assertion::inArray($method, array(self::METHOD_GET, self::METHOD_POST));

        Assertion::allString($headers);

        $this->url = $url;
        $this->referrer = $referrer;
        $this->method = $method;
        $this->headers = $headers;
        $this->data = $data;

        $this->hash();
    }

    /**
     * Returns resource URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns resource referrer of specified or NULL otherwise
     *
     * @return string|null
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * Returns HTTP method that should be used to fetch this resource
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns array of HTTP headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns HTTP request payload
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
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
    private function hash()
    {
        $this->hash = md5(serialize(array(
            $this->url, $this->method, $this->headers, $this->data,
        )));
    }
}
