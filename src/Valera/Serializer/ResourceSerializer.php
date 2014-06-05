<?php

namespace Valera\Serializer;

use Valera\Resource;

/**
 * Resource value object serializer
 */
class ResourceSerializer implements SerializerInterface
{
    /**
     * Creates array representation of resource value object
     *
     * @param \Valera\Resource $resource
     *
     * @return array
     */
    public function serialize($resource)
    {
        $params = array(
            'url' => $resource->getUrl(),
        );

        $referrer = $resource->getReferrer();
        if ($referrer !== null) {
            $params['referrer'] = $referrer;
        }

        $method = $resource->getMethod();
        if ($method !== Resource::METHOD_GET) {
            $params['method'] = $method;
        }

        $headers = $resource->getHeaders();
        if ($headers) {
            $params['headers'] = $headers;
        }

        $data = $resource->getData();
        if ($data !== null) {
            $params['data'] = $data;
        }

        return $params;
    }

    /**
     * Restores resource value object from array representation
     *
     * @param array $params
     *
     * @return \Valera\Resource
     * @throws \InvalidArgumentException
     */
    public function unserialize(array $params)
    {
        $url = $params['url'];

        if (isset($params['referrer'])) {
            $referrer = $params['referrer'];
        } else {
            $referrer = null;
        }

        if (isset($params['method'])) {
            $method = $params['method'];
        } else {
            $method = Resource::METHOD_GET;
        }

        if (isset($params['headers'])) {
            $headers = $params['headers'];
        } else {
            $headers = array();
        }

        if (isset($params['data'])) {
            $data = $params['data'];
        } else {
            $data = null;
        }

        return new Resource($url, $referrer, $method, $headers, $data);
    }
}
