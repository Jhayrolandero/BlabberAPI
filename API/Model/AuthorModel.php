<?php

include_once __DIR__ . "/../Global/Query.php";
class Author
{
    private $TABLE = "author";
    private $query;
    function __construct()
    {
        $this->query = new Query($this->TABLE);
    }

    public function addAuthor($data)
    {
        $statusMsg = [];
        $email = $data['email'];
        $count = count($this->query->selectQuery(["email"], ["email", $email]));

        if ($count > 0) {
            $statusMsg = ["status" => 409, "msg" => "Email already taken"];
            return $statusMsg;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->query->insertQuery($data);
    }
}
