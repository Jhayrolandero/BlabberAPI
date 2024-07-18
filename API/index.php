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

        if (isset($_GET['q']) && $_GET['q'] == 'fetchBlog') {
            $id = $auth->verifyToken()['payload']['id'];
            $condID = [$id, $request[1]];
            $type = "authorBlog";
        } else if (isset($_GET['q']) && $_GET['q'] == 'author') {
            $id = $auth->verifyToken()['payload']['id'];
            $condID = $id;
            $type = "authorBlogs";
        } else if (isset($request[1])) {
            $condID = $request[1];
            $type = "blogs";
        } else {
            $type = "";
            $condID = null;
        }
        $res = $GET->handleGET($request[0], $condID, $type);
        echo json_encode($res);
        break;
    default:
        http_response_code(404);
        break;

    case "POST":
        $res = $POST->handlePost($request[0]);
        // http_response_code($res['status']);
        echo json_encode($res);
        break;

    case "DELETE":
        $res = $DELETE->handleDelete($request[0], $request[1]);
        // http_response_code($res['status']);
        echo json_encode($res);
        break;

    case "PUT":
        $jsonContent = file_get_contents('php://input');
        $data = json_decode($jsonContent, true);
        $res = $PUT->handlePUT($request[0], $data, $request[1]);
        echo json_encode($res);
        break;
}
