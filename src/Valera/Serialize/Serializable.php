<?php

namespace Valera\Serialize;

interface Serializable
{
    public function accept(Serializer $serializer);
}
