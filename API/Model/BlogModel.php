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

    /**
SELECT
    b.blogID,
    b.blogTitle,
    b.blogContent,
    b.blogCreatedDate,
    a.authorID,
    a.authorName,
    GROUP_CONCAT(t.tagTitle SEPARATOR ', ') AS tags
FROM
    blog b
    INNER JOIN `author-blog` ab ON b.blogID = ab.blogID
    INNER JOIN author a ON ab.authorID = a.authorID
    LEFT JOIN `blog-tags` bt ON b.blogID = bt.blogID
    LEFT JOIN tags t ON bt.tagID = t.tagID
GROUP BY
    b.blogID, a.authorID;     */
    public function getBlog($id, $type)
    {
        if (isset($id) && $type == "blogs") {
            $condCol = ["author_blogID", $id];
        } else if (isset($id) && $type == "authorBlogs") {
            $condCol = ["$this->TABLE2.authorID", $id];
        } else if (isset($id) && $type == "authorBlog") {
            $condCol = ["$this->TABLE2.authorID = ? AND $this->TABLE2.author_blogID", [$id[0], $id[1]]];
        } else {
            $condCol = null;
        }
        // return $this->query->selectQuery();
        return $this->query2->unionQuery($cols = null, ['blogID', 'authorID'], ['blog', 'author'], $condCol);
    }

    public function deleteBlog($id)
    {
        return $this->query->deleteQuery("blogID", $id);
    }

    public function putBlog($data, $id)
    {
        return $this->query->putQuery($data, 'blogID', $id);
    }
}
