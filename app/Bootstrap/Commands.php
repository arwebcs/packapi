<?php

error_reporting(1);
ini_set('display_errors', 'Off');


return [
    "help" => "\n To use CLI, type-  php dev <command>\n
    --help                                      Display all the commands
    new::controller <ControllerName>            Creates a new controller in app/Controllers\n\n",


    "controller" => '<?php
namespace ' . $namespaceName . ';

use App\Bootstrap\{Response, Request};
use App\Database\Query;

class ' . $controllerName . '{
    
    public static function show($params)
    {
          // Do your code
    }

    public static function create(Request $req, $params)
    {
        // Do your code
    }

    public static function update(Request $req, $params)
    {
        // Do your code
    }

    public static function delete($params)
    {
        // Do your code
    }
}
?>'
];
