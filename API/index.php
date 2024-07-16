<?php


include_once "./Controller/Get.php";
include_once "./Controller/Post.php";
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
$auth = new Auth();

switch ($_SERVER['REQUEST_METHOD']) {

    case "GET":
        $id = $auth->verifyToken()['payload']['id'];

        $res = $GET->handleGET($request[0]);
        http_response_code($res['status']);
        echo json_encode($res);

        // switch ($request[0]) {
        //     case "test":
        //         echo json_encode($auth->verifyToken());
        //         break;
        // }
        break;
    default:
        http_response_code(404);
        break;

    case "POST":
        $res = $POST->handlePost($request[0]);
        http_response_code($res['status']);
        echo json_encode($res);
        break;
}
