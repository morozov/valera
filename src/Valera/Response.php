<?php
namespace Valera;

class Response implements \Serializable
{
    protected $status = 200;
    protected $content;
    protected $info = array();
    protected $resource;

    public function __construct($content, $status, Resource $resource, array $info = array())
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Content must be a string, %s given', gettype($content))
            );
        }
        $this->content = $content;
        $this->resource = $resource;
        $this->status = $status;
        $this->info = $info;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getResource()
    {
        return $this->resource;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $response = array(
            'content' => $this->content,
            'info' => $this->info,
            'resource' => serialize($this->resource)
        );
        return serialize($response);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $response = unserialize($serialized);
        $this->content = $response['content'];
        $this->info = $response['info'];
        $this->resource = unserialize($response['resource']);
    }
}
