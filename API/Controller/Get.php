<?php

include_once __DIR__ . '/../Model/TagModel.php';
class GET
{

    private $tag;
    function __construct()
    {
        $this->tag = new Tag();
    }

    public function handleGET($endpoint)
    {
        switch ($endpoint) {
            case "tag";
                return $this->tag->getTags();
        }
    }
}
