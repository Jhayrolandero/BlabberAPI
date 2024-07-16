<?php
include_once __DIR__ . "/../Global/Query.php";

class Tag
{
    private $TABLE = "tags";
    private $query;

    function __construct()
    {
        $this->query = new Query($this->TABLE);
    }
    public function getTags()
    {
        return $this->query->selectQuery();
    }
}
