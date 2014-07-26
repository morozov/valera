<?php

namespace Valera;

use Assert\Assertion;
use Valera\Value\ResourceData;

/**
 * Class Resource
 * @package Valera
 */
final class Resource extends ResourceData
{
    private $url;
    private $referrer;
    private $hash;

    /**
     * @param $url string URL of the resource
     * @param string $referrer HTTP referrer
     * @param string $method HTTP method to fetch resource
     * @param array $headers
     * @param array $payload
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        $url,
        $referrer = null,
        $method = self::METHOD_GET,
        array $headers = array(),
        $payload = null
    ) {
        if ($referrer !== null) {
            Assertion::string($url);
            $this->assertUrl($referrer);

            // make URL absolute based on referrer and relative URL
            $url = \phpUri::parse($referrer)->join($url);
        }

        $this->assertUrl($url);

        $this->url = $url;
        $this->referrer = $referrer;

        parent::__construct($method, $headers, $payload);

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
    public function getPayload()
    {
        return $this->payload;
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
            $this->url, $this->getMethod(), $this->getHeaders(), $this->getPayload(),
        )));
    }

    /**
     * Asserts that the passed string is URL
     *
     * @param string $url
     */
    private function assertUrl($url)
    {
        Assertion::regex($url, '/https?:\/\/.+/', 'Resource URL seems invalid');
    }
}
