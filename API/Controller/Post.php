<?php
include_once __DIR__ . "/../Model/AuthorModel.php";
class POST
{
    private $author;

    function __construct()
    {
        $this->author = new Author();
    }

    public function handlePost($endpoint)
    {
        switch ($endpoint) {
            case "register":
                return $this->author->addAuthor($_POST);
        }
    }
}
