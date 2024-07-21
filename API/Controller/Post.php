<?php
include_once __DIR__ . "/../Model/AuthorModel.php";
include_once __DIR__ . "/../Controller/AuthController.php";
include_once __DIR__ . "/../Model/BlogModel.php";
include_once __DIR__ . "/../Model/CommentModel.php";
class POST
{
    private $author;
    private $auth;
    private $blog;
    private $comment;
    function __construct()
    {
        $this->author = new Author();
        $this->auth = new Auth();
        $this->blog = new Blogs();
        $this->comment = new Comment();
    }

    public function handlePost($endpoint)
    {
        switch ($endpoint) {
            case "register":
                return $this->author->addAuthor($_POST);
            case "login":
                return $this->auth->loginValid($_POST);
            case "blog":
                $id = $this->auth->verifyToken()['payload']['id'];
                return $this->blog->addBlog($_POST, $id);
            case "comment";
                $id = $this->auth->verifyToken()['payload']['id'];
                return $this->comment->addComment($_POST, $id);
            default:
                return ["status" => 404, "message" => "Not Found"];
        }
    }
}
