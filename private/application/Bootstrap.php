<?php

include dirname(__DIR__).'/Sizzle/AutoLoader.php';

use Sizzle\Loader,
    Sizzle\Controller\Router,
    Sizzle\Controller;

/**
 * Additional objects can be defined here for global use within controllers. 
 */

$router = new Router;
$router->run()->load();
