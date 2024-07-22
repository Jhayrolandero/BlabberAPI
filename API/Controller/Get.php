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

                $client = $this->redis->redis();

                // Fetching pagination
                $CACHEKEY = "page:1";
                if ($client->exists($CACHEKEY)) {

                    $ids = $client->smembers($CACHEKEY);

                    foreach ($ids as $id) {
                        print_r($client->hgetall("blog:$id"));
                    }
                    // return $client->smembers($CACHEKEY);

                    // echo "Fetching from cache...\n";
                    // return json_decode($redis->get($cacheKey), true);
                }
                $value = $client->flushall();
                // $value = $this->redis->redis()->hgetall('blog:93');
                // echo $this->redis->redis()->ping();
                return $value;
        }
    }
}
