<?php

namespace Valera\Serialize;

use Valera\Content;
use Valera\Resource;
use Valera\Source;

class JsonSerializer implements Serializer
{
    public function serialize(Serializable $serializable)
    {
        return $serializable->accept($this);
    }

    public function serializeResource(Resource $resource)
    {
    }

    public function serializeSource(Source $source)
    {
    }

    public function serializeContent(Content $content)
    {
    }
}
