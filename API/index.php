<?php


include_once "./Controller/Get.php";
include_once "./Controller/Post.php";
include_once "./Controller/Delete.php";
include_once "./Controller/Put.php";
include_once "./Controller/AuthController.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Cache-Control: no-cache, no-store, must-revalidate");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {


    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

//Converts request link to array
if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
} else {
    http_response_code(404);
}

$GET = new GET();
$POST = new POST();
$DELETE = new DELETE();
$PUT = new PUT();

$auth = new Auth();

switch ($_SERVER['REQUEST_METHOD']) {

    case "GET":

        // For author fetch and edit a specific blog
        if (isset($_GET['q']) && $_GET['q'] == 'fetchBlog') {
            $id = $auth->verifyToken()['payload']['id'];
            $condID = [$id, $request[1]];
            $type = "authorBlog";
        }

        // For fetching author's blogs
        else if (isset($_GET['q']) && $_GET['q'] == 'author') {
            $id = $auth->verifyToken()['payload']['id'];
            // echo $id;
            $condID = $id;
            $type = "authorBlogs";
        }

        // Fetching read more in reading blog 
        else if (isset($_GET['q']) && $_GET['q'] == 'read' && isset($request[1])) {
            $condID = $request[1];
            $type = "readMoreExc";
        }

        // Fetching read more 
        else if (isset($_GET['q']) && $_GET['q'] == 'read') {
            $condID = null;
            $type = "readMore";
        }


        // Public API to fetch a specific BLog
        else if (isset($request[1])) {
            $condID = $request[1];
            $type = "blogs";
        }

        // For searching
        else if (isset($_GET['s'])) {
            $type = "searchBlog";
            $condID = $_GET['s'];
        } else if ($request[0] === 'profile' && isset($request[1])) {
            $type = '';
            $condID = $request[1];
        } else if ($request[0] === 'profile') {
            $type = '';
            $id = $auth->verifyToken()['payload']['id'];
            $condID = $id;
        }

        // Public API to fetch blogs for homepage
        else {
            $type = "";
            $condID = null;
        }
        $res = $GET->handleGET($request[0], $condID, $type);
        // http_response_code($res['status']);
        echo json_encode($res);
        break;
    case "POST":
        $res = $POST->handlePost($request[0]);
        // http_response_code($res['status']);
        echo json_encode($res);
        break;
    case "DELETE":
        $id = $auth->verifyToken()['payload']['id'];
        $res = $DELETE->handleDelete($request[0], $request[1]);
        // http_response_code($res['status']);
        echo json_encode($res);
        break;
    case "PUT":
        $id = $auth->verifyToken()['payload']['id'];
        $jsonContent = file_get_contents('php://input');
        $data = json_decode($jsonContent, true);
        $res = $PUT->handlePUT($request[0], $data, $request[1]);
        echo json_encode($res);
        break;
    default:
        http_response_code(404);
        break;
}
