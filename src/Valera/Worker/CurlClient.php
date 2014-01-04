<?php

namespace Valera\Worker;
use Valera\Content;
use Valera\Resource;
use RollingCurl\RollingCurl;
use RollingCurl\Request;
use Valera\Response;

class CurlClient extends HttpClient
{
    protected $options;

    /**
     * @param array $options
     * @throws \InvalidArgumentException
     * @return CurlClient
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException("options must be an array");
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Override and add options
     *
     * @param array $options
     * @throws \InvalidArgumentException
     * @return CurlClient
     */
    public function addOptions($options)
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException("options must be an array");
        }
        $this->options = $options + $this->options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $rollingCurl = new RollingCurl();
        if (!empty($this->options)) {
            $rollingCurl->addOptions($this->options);
        }
        if (!empty($this->getResources)) {

            foreach ($this->getResources as $url => $resource) {
                $rollingCurl->get($url, $resource->getHeaders());
            }
            $rollingCurl
                ->setCallback(
                    function(Request $request, RollingCurl $rollingCurl) {
                        $resource = $this->getResources[$request->getUrl()];
                        if ($request->getResponseErrno()===0) {
                            $callback = $this->successCallback;
                        } else {
                            $callback = $this->failureCallback;
                        }
                        $info = $request->getResponseInfo();
                        $response = new Response($request->getResponseText(), $info['http_code'], $resource, $info);
                        if (is_callable($callback)) {
                            $callback($response);
                        }
                        $completeCallback = $this->completeCallback;
                        if (is_callable($completeCallback)) {
                            $completeCallback($response);
                        }
                    }
                )
                ->setSimultaneousLimit(10)
                ->execute();
            ;
        }
    }
}
