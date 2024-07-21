<?php

include_once __DIR__ . '/../Model/TagModel.php';
include_once __DIR__ . '/../Model/BlogModel.php';
include_once __DIR__ . '/../Model/CommentModel.php';
include_once __DIR__ . '/../Model/AuthorModel.php';
include_once __DIR__ . '/../Controller/AuthController.php';
class GET
{

    private $tag;
    private $blog;
    private $comment;
    private $author;
    private $auth;
    function __construct()
    {
        $this->tag = new Tag();
        $this->blog = new Blogs();
        $this->comment = new Comment();
        $this->author = new Author();
        $this->auth = new Auth();
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
            case "profile":
                return $this->author->getAuthorProfile($id);
        }
    }
}
