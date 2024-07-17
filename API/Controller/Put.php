<?php
include_once __DIR__ . '/../Model/BlogModel.php';

class PUT
{
    private $blog;
    function __construct()
    {
        $this->blog = new Blogs();
    }


    public function handlePUT($endpoint, $data, $id)
    {
        switch ($endpoint) {
            case "blog":
                return $this->blog->putBlog($data, $id);
        }
    }
}
