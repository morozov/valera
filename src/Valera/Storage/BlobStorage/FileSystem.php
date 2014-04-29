<?php

namespace Valera\Storage\BlobStorage;

use Valera\Resource;
use Valera\Storage\BlobStorage;

class FileSystem implements BlobStorage
{
    protected $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

    public function create(Resource $resource, $data)
    {
        $path = $this->getPath($resource);
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, $data);
    }

    public function retrieve(Resource $resource)
    {
        $path = $this->getPath($resource);
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    public function delete(Resource $resource)
    {
        $path = $this->getPath($resource);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    protected function getPath(Resource $resource)
    {
        $url = $resource->getUrl();
        $url = preg_replace('/^[a-z0-9]+:\/\/', '', $url);
        $sections = explode('/', $url);
        $sections = array_map(function ($section) {
            return rawurlencode($section);
        }, $sections);
        array_unshift($sections, $this->root);

        return implode(DIRECTORY_SEPARATOR, $sections);
    }

    public function clean()
    {
        exec('rm -r ' . escapeshellarg($this->root));
    }
}
