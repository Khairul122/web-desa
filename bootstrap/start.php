<?php

require_once __DIR__ . '/autoload.php';

\App\Core\Session::start();

$request = \App\Core\Request::capture();

$router = require ROUTES_PATH . '/web.php';

$response = $router->dispatch($request);

$response->send();
