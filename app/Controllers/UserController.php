<?php

namespace App\Controllers;

use App\Bootstrap\{Response, Request, Validation};
use App\Database\Query;

use Devker\Vaults\Vaults;

class UserController
{
    public static function show($params)
    {
        $response = new Response(["statusCode" => 200, "message" => "Get method"], 200);
        echo $response->responseJSON();
    }

    public static function create(Request $req, $params)
    {
        $response = new Response(["statusCode" => 201, "message" => "Post method"], 201);
        echo $response->responseJSON();
    }

    public static function update(Request $req, $params)
    {
        $response = new Response(["statusCode" => 201, "message" => "Put method"], 201);
        echo $response->responseJSON();
    }

    public static function delete($params)
    {
        $response = new Response(["statusCode" => 201, "message" => "Delete method"], 201);
        echo $response->responseJSON();
    }

    public static function test(Request $req, $params)
    {
        echo Vaults::stringEncryptB64("API");
    }
}
