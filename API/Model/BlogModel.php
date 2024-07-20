<?php
include_once __DIR__ . "/../Global/Query.php";

class Blogs
{
    private $TABLE = "blog";
    private $TABLE2 = "`author-blog`";
    private $TABLE3 = "`blog-tags`";
    private $query;
    private $query2;
    private $query3;
    function __construct()
    {
        $this->query = new Query($this->TABLE);
        $this->query2 = new Query($this->TABLE2);
        $this->query3 = new Query($this->TABLE3);
    }

    public function addBlog($data, $id)
    {
        $origData = $data;
        unset($data['tagID']);

        $res = $this->query->insertQuery($data);

        if ($res['status'] != 200) return $res;

        $blogID = $this->query->getLastID($this->TABLE)[0]['AUTO_INCREMENT'] - 1;

        $blog_author = ["blogID" => $blogID, "authorID" => $id];

        foreach ($origData['tagID'] as $tagID) {
            $blog_tag = ["blogID" => $blogID, "tagID" => $tagID];
            $tagRes = $this->query3->insertQuery($blog_tag);
            if ($tagRes['status'] != 200) return $tagRes;
        }

        return $this->query2->insertQuery($blog_author);
    }
    public function getBlog($id, $type)
    {
        // Public API to fetch a specific BLog
        if (isset($id) && $type == "blogs") {
            $like = false;
            $condCol = ["ab.author_blogID", $id];
        }
        // For fetching author's blogs
        else if (isset($id) && $type == "authorBlogs") {
            $like = false;
            $condCol = ["a.authorID", $id];
        }
        // For author fetch and edit a specific blog
        else if (isset($id) && $type == "authorBlog") {
            $like = false;
            $condCol = ["a.authorID = ? AND ab.author_blogID", [$id[0], $id[1]]];
        } else if ($type == "searchBlog") {
            $like = true;
            $condCol = ["LOWER(b.blogContent) LIKE ? OR LOWER(b.blogTitle) LIKE ?", ["%$id%", "%$id%"]];
        }
        // Public API to fetch blogs for homepage
        else {
            $like = false;
            $condCol = null;
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
                    ab.author_blogID,
                    GROUP_CONCAT(t.tagID SEPARATOR ', ') AS tags
                FROM
                    blog b
                    INNER JOIN `author-blog` ab ON b.blogID = ab.blogID
                    INNER JOIN author a ON ab.authorID = a.authorID
                    LEFT JOIN `blog-tags` bt ON b.blogID = bt.blogID
                    LEFT JOIN tags t ON bt.tagID = t.tagID";

        // return $this->query->selectQuery();
        return $this->query->executeQuery($sql, $condCol, "b.blogID, a.authorID", $like);
        // return $this->query2->unionQuery($cols = null, ['blogID', 'authorID'], ['blog', 'author'], $condCol);
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
