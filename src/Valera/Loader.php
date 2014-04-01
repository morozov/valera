<?php

namespace Valera;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;
use Valera\Loader\LoaderInterface;
use Valera\Result\Proxy as Result;

class Loader implements LoaderInterface
{
    protected $httpClient;
    
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function load(Resource $resource, Result $result)
    {
        $response = $this->sendRequest($resource);
        $this->processResponse($response, $result);
    }

    protected function sendRequest(Resource $resource)
    {
        return $this->httpClient->createRequest(
            $resource->getMethod(),
            $resource->getUrl(),
            $resource->getHeaders(),
            $resource->getData()
        )->send();
    }

    protected function processResponse(Response $response, Result $result)
    {
        if ($response->isError()) {
            $message = $response->getStatusCode();
            $result->fail($message);
        } else {
            $body = $response->getBody(true);
            $result->succeed($body);
        }
    }
}
