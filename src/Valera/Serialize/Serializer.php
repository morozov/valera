<?php

namespace Valera\Serialize;

use Valera\Content;
use Valera\Resource;
use Valera\Source;

interface Serializer
{
    public function serialize(Serializable $serializable);

    public function serializeResource(Resource $resource);

    public function serializeSource(Source $source);

    public function serializeContent(Content $content);
}
