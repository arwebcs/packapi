<?php

namespace App\Bootstrap;

use App\Bootstrap\{Response, Controllers};

class Router
{
    private static $handlers = [];
    private const METHOD_GET = "GET";
    private const METHOD_POST = "POST";
    private const METHOD_PUT = "PUT";
    private const METHOD_DELETE = "DELETE";

    public static function addMethod($method, $path, $handler = null): void
    {
        self::$handlers[] = [
            'path' => $path,
            'methodName' => $method,
            'handler' => $handler,
        ];
    }
    public static function apiMethods($path, $handler = null): void
    {
        switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
            case 'GET':
                $handler[] = 'show';
                self::addMethod(self::METHOD_GET, $path, $handler);
                break;
            case 'POST':
                $handler[] = 'create';
                self::addMethod(self::METHOD_POST, $path, $handler);
                break;
            case 'PUT':
                $handler[] = 'update';
                self::addMethod(self::METHOD_PUT, $path, $handler);
                break;
            case 'DELETE':
                $handler[] = 'delete';
                self::addMethod(self::METHOD_DELETE, $path, $handler);
                break;
            default:
                self::addMethod('', $path, $handler);
                break;
        }
    }

    public static function get($path, $handler = null): void
    {
        self::addMethod(self::METHOD_GET, $path, $handler);
    }
    public static function post($path, $handler = null): void
    {
        self::addMethod(self::METHOD_POST, $path, $handler);
    }
    public static function put($path, $handler = null): void
    {
        self::addMethod(self::METHOD_PUT, $path, $handler);
    }
    public static function delete($path, $handler = null): void
    {
        self::addMethod(self::METHOD_DELETE, $path, $handler);
    }

    public static function run()
    {
        $urlMatched = 0;
        $requestPath = parse_url($_SERVER['REQUEST_URI']);
        $requestURI = trim($requestPath['path'], '/');
        $requestMethod =  $_SERVER['REQUEST_METHOD'];

        $position = strpos($requestURI, "/api/") + 4;

        $part =  substr($requestURI, $position);
        $handle = array();
        $params = array();


        foreach (self::$handlers as $urlHandler) {
            $uri = $urlHandler['path'];
            $actualPart = array_filter(explode("/", $part));
            $actualURI = array_filter(explode("/", $uri));

            if (sizeof($actualPart) === sizeof($actualURI)) {
                $finalArray = array_combine($actualURI, $actualPart);

                foreach ($finalArray as $key => $value) {
                    if (strpos($key, ':') !== false) {
                        unset($finalArray[$key]);
                    }
                }

                $keys = array_keys($finalArray);
                $values = array_values($finalArray);
                sort($keys);
                sort($values);

                if ($keys === $values) {
                    if ($urlHandler['methodName'] === $requestMethod) {
                        $arrCombine = array_combine(array_filter(explode("/", $uri)), array_filter(explode("/", $part)));

                        foreach ($arrCombine as $key => $value) {

                            if (strpos($key, ':') === 0) {
                                $params[str_replace(":", "", $key)] = $value;
                            }
                        }
                        $urlMatched = 1;
                        $handle = $urlHandler['handler'];
                        array_push($handle, $urlHandler['methodName']);
                        break;
                    } else {
                        $urlMatched = 2;
                    }
                }
            }
        }

        switch ($urlMatched) {
            case 1:
                Controllers::invoke($handle[0], $handle[1], $handle[2], $requestPath['query'] ? $requestPath['query'] : '', $params);
                break;
            case 2:
                $response = new Response(['statusCode' => 405, 'message' => 'Request method not allowed'], 405);
                echo $response->responseJSON();
                break;
            case 3:
                $response = new Response(['statusCode' => 500, 'message' => 'Internal server error'], 500);
                echo $response->responseJSON();
                break;
            default:
                $response = new Response(['statusCode' => 403, 'message' => 'URL link not matched'], 403);
                echo $response->responseJSON();
                break;
        }
    }
}
