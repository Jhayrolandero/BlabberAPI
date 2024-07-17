<?php


include_once __DIR__ . '/../Model/BlogModel.php';
class DELETE
{

    private $blog;
    function __construct()
    {
        $this->blog = new Blogs();
    }


    public function handleDelete($endpoint, $id)
    {
        switch ($endpoint) {
            case "blog":
                return $this->blog->deleteBlog($id);
        }
    }
}
