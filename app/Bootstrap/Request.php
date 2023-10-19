<?php

namespace App\Bootstrap;

use App\Bootstrap\Response;

class Request
{
    protected $data = array();

    public function __construct()
    {
        if (file_get_contents("php://input")) {
            if (self::jsonValidator(file_get_contents("php://input"))) {
                foreach (json_decode(file_get_contents("php://input"), true) as $key => $value) {
                    $this->data[$key] = $value;
                }
            } else {
                $response = new Response(['statusCode' => 400, 'message' => 'Not a valid JSON'], 400);
                $this->data = json_decode($response->responseJSON(), true);
            }
        } else {
            foreach ($_POST as $key => $val) {
                $this->data[$key] = $val;
            }
            foreach ($_FILES as $fileKey => $fileVal) {
                $this->data[$fileKey] = $fileVal;
            }
        }
    }


    public static function jsonValidator($data)
    {
        if (!empty($data)) {
            return is_string($data) && is_array(json_decode($data, true))
                ? true
                : false;
        }
        return false;
    }

    public function input($key = "")
    {
        return $key == "" ? $this->data : $this->data[$key];
    }
}
