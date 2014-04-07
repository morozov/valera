<?php

namespace Valera\Serialize;

use Valera\Content;
use Valera\Resource;
use Valera\Source;

class ArraySerializer implements Serializer
{
    public function serialize(Serializable $serializable)
    {
        return $serializable->accept($this);
    }

    public function serializeResource(Resource $resource)
    {
        $params['url'] = $resource->getUrl();

        $method = $resource->getMethod();
        if ($method !== Resource::METHOD_GET) {
            $params['method'] = $method;
        }

        $headers = $resource->getHeaders();
        if ($headers) {
            $params['headers'] = $headers;
        }

        $data = $resource->getData();
        if ($data) {
            $params['data'] = $data;
        }

        return $params;
    }

    public function serializeSource(Source $source)
    {
        return array(
            'resource' => $this->serializeResource($source->getResource()),
            'type' => $source->getType(),
        );
    }

    public function serializeContent(Content $content)
    {
        return array(
            'content' => $content->getContent(),
            'type' => $content->getType(),
            'resource' => $this->serialize($content->getResource()),
        );
    }
}
