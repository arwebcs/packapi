<?php

use App\Bootstrap\Router;
use App\Controllers\UserController;

/*
* Define routes here.
*/

Router::apiMethods('users', [UserController::class]);
Router::post("jio", [UserController::class, "test"]);

/*
 * 
 * Use these apis like this :-> 
 * 
 *   1. If the api files are in the root directory: <your_domain_url>/api/<your specific routes>
 *   2. If the api files are in any sub-directory: <your_domain_url>/path/to/folder/api/<your specific routes>
 *
 */