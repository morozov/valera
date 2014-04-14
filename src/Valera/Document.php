<?php

namespace Valera;

class Document
{
    protected $id;
    protected $type;
    protected $data = array();

    public function __construct($id, array $data, $type = null)
    {
        $this->id = $id;
        $this->data = $data;
        $this->type = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getBlobs()
    {
        $blobs = array();
        array_walk_recursive($this->data, function($value) use (&$blobs) {
            if ($value instanceof Blob) {
                $blobs[] = $value;
            }
        });

        return $blobs;
    }
}
