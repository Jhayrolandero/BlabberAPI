<?php

include_once __DIR__ . '/../Model/TagModel.php';
include_once __DIR__ . '/../Model/BlogModel.php';
include_once __DIR__ . '/../Model/CommentModel.php';
include_once __DIR__ . '/../Model/AuthorModel.php';
include_once __DIR__ . '/../Model/Redis.php';
include_once __DIR__ . '/../Controller/AuthController.php';
class GET
{

    private $tag;
    private $blog;
    private $comment;
    private $author;
    private $auth;
    private $redis;
    function __construct()
    {
        $this->tag = new Tag();
        $this->blog = new Blogs();
        $this->comment = new Comment();
        $this->author = new Author();
        $this->auth = new Auth();
        $this->redis = new Redis();
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
            case "test":
                // json_decode($redis->get($cacheKey), true);
                // $this->redis->redis()->set('foo:1', 'bar');
                $value = $this->redis->redis()->hgetall('blog:93');
                // echo $this->redis->redis()->ping();
                return $value;
        }
    }
}
