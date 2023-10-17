<?php

namespace App\Bootstrap;

use App\Bootstrap\Environment;

class Token
{
    protected static function generateToken($headers = "", $payload = "", $secret = 'secret')
    {
        $headersEncoded = self::encodeURL(json_encode($headers));
        $payloadEncoded = self::encodeURL(json_encode($payload));
        $signature = hash_hmac('SHA256', "$headersEncoded.$payloadEncoded", $secret, true);
        $signature_encoded = self::encodeURL($signature);
        $bearerToken = "$headersEncoded.$payloadEncoded.$signature_encoded";
        return $bearerToken;
    }

    protected static function tokenValiditatonCheck($token = "", $secret = 'secret')
    {
        $tokenParts = explode('.', $token);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        $expiration = json_decode($payload)->exp;
        $isTokenExpired = ($expiration - time()) < 0;
        $base64URLHeader = self::encodeURL($header);
        $base64URLPayload = self::encodeURL($payload);
        $signature = hash_hmac('SHA256', $base64URLHeader . "." . $base64URLPayload, $secret, true);
        $base64URLSignature = self::encodeURL($signature);
        $isSignatureValid = ($base64URLSignature === $signatureProvided);
        if ($isTokenExpired || !$isSignatureValid) {
            return false;
        } else {
            return true;
        }
    }

    protected static function encodeURL($data = "")
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generateBearerToken($name, $algorithm = "HS256", $type = "JWT")
    {
        $env = new Environment();
        $tokenSecretKey = $env->env('TOKEN_SECRET_KEY');
        $tokenExpiredTime = $env->env('TOKEN_EXPIRED_TIME');

        $headers = array('alg' => $algorithm, 'typ' => $type);
        $payload = array('name' => $name, 'exp' => (time() + $tokenExpiredTime));
        $token = self::generateToken($headers, $payload, $tokenSecretKey);
        return $token;
    }

    public static function validBearerToken()
    {
        $env = new Environment();
        $tokenSecretKey = $env->env('TOKEN_SECRET_KEY');

        $headers = getallheaders();
        $bearerToken = trim(substr($headers['Authorization'], 6));
        if (($bearerToken == "") || (empty($bearerToken)) || ($bearerToken == null)) {
            $response = new Response(["statusCode" => 422, "message" => "Unproccesseble entity", "error" => "Please provide bearer token"], 422);
            echo $response->responseJSON();
            die();
        } else {
            if (self::tokenValiditatonCheck($bearerToken, $tokenSecretKey)) {
                return true;
            } else {
                $response = new Response(["statusCode" => 401, "message" => "Unauthorized", "error" => "Access denied"], 401);
                echo $response->responseJSON();
                die();
            }
        }
    }

    public static function validBasicAuth($username, $password)
    {
        $encryptedAuth = base64_encode($username . ":" . $password);
        $headers = getallheaders();
        $basicAuth = trim(substr($headers['Authorization'], 5));
        if (($basicAuth == "") || (empty($basicAuth)) || ($basicAuth == null)) {
            $response = new Response(["statusCode" => 422, "message" => "Unproccesseble entity", "error" => "Please provide basic authentication"], 422);
            echo $response->responseJSON();
            die();
        } else if (($encryptedAuth == "") || (empty($encryptedAuth)) || ($encryptedAuth == null)) {
            $response = new Response(["statusCode" => 422, "message" => "Unproccesseble entity", "error" => "Please provide basic authentication"], 422);
            echo $response->responseJSON();
            die();
        } else {
            if ($basicAuth === $encryptedAuth) {
                return true;
            } else {
                $response = new Response(["statusCode" => 401, "message" => "Unauthorized", "error" => "Access denied"], 401);
                echo $response->responseJSON();
                die();
            }
        }
    }
}
