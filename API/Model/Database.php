<?php
$env = parse_ini_file('.env');
//SET DEFAULT TIME ZONE
date_default_timezone_set("Asia/Manila");
//SET TIME LIMIT ON REQUESTs
set_time_limit(1000);

define("SERVER", $env["SERVER"]);
define("DATABASE", $env["DB_NAME"]);
define("USER", $env["USER"]);
define("PASSWORD", $env["PASSWORD"]);
define("DRIVER", $env["DRIVER"]);
class Connection
{
    private $connectionString = DRIVER . ":host=" . SERVER . ";dbname=" . DATABASE . "; charset=utf8mb4";
    private $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    public function connect()
    {
        return new \PDO($this->connectionString, USER, PASSWORD, $this->options);
    }
}
