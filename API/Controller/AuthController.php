<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

include_once  __DIR__ . "/../Global/Query.php";
require_once(__DIR__ . '/../../vendor/autoload.php');
class Auth
{

    private $query;

    function __construct()
    {
        $this->query = new Query("author");
    }

    private function generateToken($aID)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (1440 * 60);

        $env = parse_ini_file('.env');
        $secretKey = $env["GCFAMS_API_KEY"];
        $token = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "id" => $aID,
        );

        return array(
            "status" => 200,
            "message" => "Success",
            "token" => JWT::encode($token, $secretKey, 'HS512')
        );
    }
    public function loginValid($data)
    {
        $res = $this->query->selectQuery(["email", "password", "authorID"], ["email", $data["email"]]);

        if (empty($res["data"])) {
            return ["status" => 401, "message" => "Invalid Email or Password"];
        }

        if ($res["status"] == 200) {
            $passValid = password_verify($data["password"], $res["data"][0]["password"]);

            if ($passValid) {
                return $this->generateToken($res["data"][0]["authorID"]);
            } else {
                return ["status" => 401, "message" => "Invalid Email or Password"];
            }
        } else {
            return ["status" => 500, "message" => $res["message"]];
        }
    }

    public function verifyToken()
    {

        $env = parse_ini_file('.env');

        // Prevent Outsiders from accessing the API
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo "Unauthorized Access nigga!";
            exit;
        }
        //Check existence of token
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Token not found in request';
            exit;
        }

        //Check header
        $jwt = $matches[1];
        if (!$jwt) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Token is missing but header exist';
            exit;
        }

        // return $matches;
        //Separate token to 3 parts
        $jwtArr = explode('.', $jwt);

        // return $jwtArr;
        $headers = new stdClass();
        // $env = parse_ini_file('.env');
        $secretKey = $env["GCFAMS_API_KEY"];

        //Decode received token

        try {
            $payload = JWT::decode($jwt, new Key($secretKey, 'HS512'), $headers);
            return array(
                "code" => 200,
                "payload" =>
                array(
                    "id" => $payload->id,
                )
            );
        } catch (\Throwable $th) {
            // throw $th;
            header('HTTP/1.0 403 Forbidden');
            echo $th;
            exit;
        }
    }
}
