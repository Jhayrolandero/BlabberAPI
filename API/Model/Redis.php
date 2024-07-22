<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

class Redis
{

    private $redis;
    function __construct()
    {
        $this->redis = new Predis\Client();
    }

    function redis()
    {
        return $this->redis;
    }
}
