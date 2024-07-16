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

    public function getBlog()
    {
        return $this->query->selectQuery();
    }
}
