<?php
include_once __DIR__ . "/../Global/Query.php";
include_once __DIR__ . "/../Model/Redis.php";

class Blogs
{
    private $TABLE = "blog";
    private $TABLE2 = "`author-blog`";
    private $TABLE3 = "`blog-tags`";
    private $query;
    private $query2;
    private $query3;
    private $redis;
    private $client;

    function __construct()
    {
        $this->query = new Query($this->TABLE);
        $this->query2 = new Query($this->TABLE2);
        $this->query3 = new Query($this->TABLE3);
        $this->redis = new Redis();
        $this->client = $this->redis->redis();
    }

    public function addBlog($data, $id)
    {
        $origData = $data;
        $data['authorID'] = $id;
        unset($data['tagID']);

        $res = $this->query->insertQuery($data);

        if ($res['status'] != 200) return $res;

        $blogID = $this->query->getLastID($this->TABLE)[0]['AUTO_INCREMENT'] - 1;


        foreach ($origData['tagID'] as $tagID) {
            $blog_tag = ["blogID" => $blogID, "tagID" => $tagID];
            $tagRes = $this->query3->insertQuery($blog_tag);
            if ($tagRes['status'] != 200) return $tagRes;
        }

        $this->client->flushall();
        return $tagRes;
        // return $this->query->insertQuery($blog_author);
    }
    public function getBlog($id, $type)
    {
        $like = false;
        $random = false;
        $condCol = null;
        $limit = 0;
        $not = false;
        $offset = 0;
        $currPage = 1;
        // Public API to fetch a specific BLog
        if (isset($id) && $type === "blogs") {
            $condCol = ["b.blogID", $id];
        }

        // Public pagination
        else if ($type === 'page') {
            // $condCol = [""]
            $currPage = $id;
            $pages = 25;
            $limit = $pages;
            $offset = ($id - 1) * $pages;

            if ($this->client->exists("page:$currPage")) {
                $ids = $this->client->smembers("page:$currPage");

                $data = [];
                foreach ($ids as $id) {
                    array_push($data, $this->client->hgetall("blog:$id"));
                }

                return ["status" => 200, "message" => "From Cache", "data" => $data];
            }
        }
        // For fetching author's blogs
        else if (isset($id) && $type === "authorBlogs") {
            $condCol = ["a.authorID", $id];
        }
        // For author fetch and edit a specific blog
        else if (isset($id) && $type === "authorBlog") {
            $condCol = ["a.authorID = ? AND b.blogID", [$id[0], $id[1]]];
            // $condCol = ["a.authorID = ? AND ab.author_blogID", [$id[0], $id[1]]];
        }
        // Searching Blog
        else if ($type === "searchBlog") {
            $like = true;
            $condCol = ["LOWER(b.blogTitle) LIKE ?", "%$id%"];
            // $condCol = ["LOWER(b.blogContent) LIKE ? OR LOWER(b.blogTitle) LIKE ?", ["%$id%", "%$id%"]];
        }
        // Read more
        else if ($type === "readMore") {
            $random = true;
            $condCol = ["b.public", 1];
            $limit = 10;
        }
        // Read momre exclude
        else if ($type === "readMoreExc") {
            $random = true;
            $condCol = ["b.blogID", [$id]];
            $limit = 10;
            $not = true;
        }

        $sql = "SELECT
                    b.blogID,
                    b.blogTitle,
                    b.blogContent,
                    b.blogCreatedDate,
                    b.tagID,
                    b.public,
                    a.authorID,
                    a.authorName,
                    GROUP_CONCAT(t.tagID SEPARATOR ', ') AS tags
                FROM
                    blog b
                    INNER JOIN author a ON b.authorID = a.authorID
                    LEFT JOIN `blog-tags` bt ON b.blogID = bt.blogID
                    LEFT JOIN tags t ON bt.tagID = t.tagID";

        $res = $this->query->executeQuery($sql, $condCol, "b.blogID, a.authorID", $like, $random, $limit, $not, $offset);

        if ($type === 'page') {

            if ($res['status'] != 200) return $res;

            foreach ($res['data'] as $data) {

                $bID = $data['blogID'];
                $bTitle = $data['blogTitle'];
                $bContent = $data['blogContent'];
                $bCreatedDate = $data['blogCreatedDate'];
                $btagID = $data['tagID'];
                $bPublic = $data['public'];
                $bAID = $data['authorID'];
                $bAName = $data['authorName'];
                $bTags = $data['tags'];

                $this->client->sadd("page:$currPage", $bID);

                $this->client->hset("blog:$bID", "blogID", $bID);
                $this->client->hset("blog:$bID", "blogTitle", $bTitle);
                $this->client->hset("blog:$bID", "blogContent", $bContent);
                $this->client->hset("blog:$bID", "blogCreatedDate", $bCreatedDate);
                $this->client->hset("blog:$bID", "tagID", $btagID);
                $this->client->hset("blog:$bID", "public", $bPublic);
                $this->client->hset("blog:$bID", "authorID", $bAID);
                $this->client->hset("blog:$bID", "authorName", $bAName);
                $this->client->hset("blog:$bID", "tags", $bTags);
            }
        }
        return $res;
    }

    public function deleteBlog($id)
    {
        return $this->query->deleteQuery("blogID", $id);
    }

    public function putBlog($data, $id)
    {
        $tagData = ['tagID' => $data['tagID']];
        unset($data['tagID']);

        if (count($data) > 0) {
            $res = $this->query->putQuery($data, 'blogID', $id);

            if ($res['status'] != 200) return $res;
        }

        // Delete query
        $res2 = $this->query3->deleteQuery("blogID", $id);

        if ($res2['status'] != 200) return $res2;


        $insQuery = ['tagID' => $tagData['tagID'], 'blogID' => [$id]];

        return $this->query3->multipleInsertQuery($insQuery);
    }
}
