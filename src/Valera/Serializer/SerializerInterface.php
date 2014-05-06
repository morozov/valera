<?php

namespace Valera\Serializer;

/**
 * Value object serializer interface
 */
interface SerializerInterface
{
    /**
     * Creates array representation of value object
     *
     * @param mixed $object
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public function serialize($object);

    /**
     * Restores value object from array representation 
     *
     * @param array $params
     *
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function unserialize(array $params);
}
