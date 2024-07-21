<?php


include_once __DIR__ . '/../Model/BlogModel.php';
include_once __DIR__ . '/../Model/CommentModel.php';
class DELETE
{

    private $blog;
    private $comment;
    function __construct()
    {
        $this->blog = new Blogs();
        $this->comment = new Comment();
    }


    public function handleDelete($endpoint, $id)
    {
        switch ($endpoint) {
            case "blog":
                return $this->blog->deleteBlog($id);
            case "comment":
                return $this->comment->deleteComment($id);
        }
    }
}
