<?php

include_once __DIR__ . '/../Model/TagModel.php';
include_once __DIR__ . '/../Model/BlogModel.php';
include_once __DIR__ . '/../Model/CommentModel.php';
class GET
{

    private $tag;
    private $blog;
    private $comment;
    function __construct()
    {
        $this->tag = new Tag();
        $this->blog = new Blogs();
        $this->comment = new Comment();
    }

    public function handleGET($endpoint, $id, $type)
    {
        switch ($endpoint) {
            case "tag":
                return $this->tag->getTags();
            case "blog":
                return $this->blog->getBlog($id, $type);
            case "comment":
                return $this->comment->getComment($id);
        }
    }
}
