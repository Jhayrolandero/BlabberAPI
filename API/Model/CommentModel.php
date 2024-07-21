<?php
include_once __DIR__ . "/../Global/Query.php";


class Comment
{
    private $TABLE = 'comment';
    private $query;

    function __construct()
    {
        $this->query = new Query($this->TABLE);
    }


    public function addComment($data, $id)
    {
        $data["authorID"] = $id;

        $cID = $this->query->getLastID($this->TABLE)[0]['AUTO_INCREMENT'];
        // var_dump($data);
        $res = $this->query->insertQuery($data);
        if ($res['status'] != 200) return $res;

        $sql = "SELECT 
                    c.commentContent, c.commentID, c.commentDate, a.authorName 
                FROM 
                    `comment` c
                INNER JOIN 
                    `author` a on c.authorID = a.`authorID`";

        $condCol = ["c.commentID", $cID];

        return $this->query->executeQuery($sql, $condCol);
        // return $this->query->insertQuery($data);
    }

    public function getComment($bID)
    {


        $sql = "SELECT 
                    c.commentContent, c.commentID, c.commentDate, a.authorName 
                FROM 
                    `comment` c
                INNER JOIN 
                    `author` a on c.authorID = a.`authorID`";

        $condCol = ["c.blogID", $bID];

        return $this->query->executeQuery($sql, $condCol);
    }
}
