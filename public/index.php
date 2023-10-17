<?php

require_once __DIR__ . "/../app/Config/system.php";
require_once __DIR__ . "/../app/Config/header.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../routes/api.php";


use App\Bootstrap\Router;

Router::run();
