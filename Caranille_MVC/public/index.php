<?php

require_once(__DIR__ . '/../src/Controllers/HomeController.php');

$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

switch ($request_uri[0]) {
    case '/':
        $controller = new HomeController();
        $controller->index();
        break;
    default:
        http_response_code(404);
        echo 'Page not found';
        break;
}
