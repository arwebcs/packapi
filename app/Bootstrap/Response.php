<?php

namespace App\Bootstrap;

class Response
{

    private $response = array();
    private $responseCode;

    public function __construct($response = array(), $responseCode = 200)
    {
        $this->response = $response;
        $this->responseCode = $responseCode;
    }

    public function responseJSON()
    {
        http_response_code($this->responseCode);
        return json_encode($this->response, JSON_PRETTY_PRINT);
    }
    public function responseArray()
    {
        return $this->response;
    }
}
