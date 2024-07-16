<?php

include_once __DIR__ . '/../Model/TagModel.php';
include_once __DIR__ . '/../Model/BlogModel.php';
class GET
{

    private $tag;
    private $blog;
    function __construct()
    {
        $this->tag = new Tag();
        $this->blog = new Blogs();
    }

    public function handleGET($endpoint)
    {
        switch ($endpoint) {
            case "tag":
                return $this->tag->getTags();
            case "blog":
                return $this->blog->getBlog();
        }
    }
}
