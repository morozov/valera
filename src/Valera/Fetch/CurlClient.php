<?php

namespace Valera\Fetch;
use Valera\Content;
use Valera\Resource;
use RollingCurl\RollingCurl;
use RollingCurl\Request;

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
    public function fetch()
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
                        $callback = $this->successCallback;
                        $resource = $this->getResources[$request->getUrl()];
                        $content = new Content(strval($request->getResponseText()), $resource);
                        $callback($content);
                    }
                )
                ->setSimultaneousLimit(10)
                ->execute();
            ;
        }
    }
}
