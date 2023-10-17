<?php

namespace App\Bootstrap;

use Devker\Vaults\Vaults;

class Controllers
{
    private static function queryStringSanitizer($queryString = '')
    {
        if (empty($queryString)) {
            return [];
        } else {
            $queryStringArr = [];
            $splitParams = explode('&', $queryString);
            foreach ($splitParams as $values) {
                $queryStringArr[trim(explode('=', $values)[0])] = trim(
                    explode('=', $values)[1]
                );
            }
            return json_decode(Vaults::convertToJSON($queryStringArr), true);
        }
    }

    private static function paramSanitizer($param = [])
    {
        if (sizeof($param) == 0) {
            return [];
        } else {
            return json_decode(Vaults::convertToJSON($param), true);
        }
    }

    public static function invoke($controllerName, $method, $reqMethod, $queryString = '', $params = [])
    {
        $parameters = Vaults::convertToJSON(array_merge(self::queryStringSanitizer($queryString), self::paramSanitizer($params)));

        if (substr($controllerName, -10) === "Controller") {
            switch ($reqMethod) {
                case "GET":
                    return $controllerName::$method(json_decode($parameters));
                    break;
                case "POST":
                    return $controllerName::$method(new Request(), json_decode($parameters));
                    break;
                case "PUT":
                    return $controllerName::$method(new Request(), json_decode($parameters));
                    break;
                case "DELETE":
                    return $controllerName::$method(json_decode($parameters));
                    break;
                default:
                    break;
            }
        } else {
            die("Class name and File name must ends with 'Controller'");
        }
    }
}
