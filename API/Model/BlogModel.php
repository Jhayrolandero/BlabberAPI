<?php
include_once __DIR__ . "/../Global/Query.php";

class Blogs
{
    private $TABLE = "blog";
    private $TABLE2 = "`author-blog`";
    private $query;
    private $query2;
    function __construct()
    {
        $this->query = new Query($this->TABLE);
        $this->query2 = new Query($this->TABLE2);
    }

    public function addBlog($data, $id)
    {
        $res = $this->query->insertQuery($data);

        // $data["blogID"] = $id;
        if ($res["status"] == 200) {
            $blogID = $this->query->getLastID($this->TABLE)[0]['AUTO_INCREMENT'] - 1;
            $blog_author = ["blogID" => $blogID, "authorID" => $id];

            return $this->query2->insertQuery($blog_author);
        } else {
            return $res;
        }
    }

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
