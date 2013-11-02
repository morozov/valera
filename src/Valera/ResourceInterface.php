<?php

namespace Valera;

/**
 * Class ResourceInterface
 */
interface ResourceInterface
{
    /**
     * Returns resource URL
     * @return string
     */
    public function getUrl();

    /**
     * Returns HTTP method that should be used to fetch this resource
     * @return string
     */
    public function getMethod();

    /**
     * Returns array of headers
     * @return array
     */
    public function getHeaders();

    /**
     * Returns data that should be send by POST or PUT method
     * @return mixed
     */
    public function getData();

}
