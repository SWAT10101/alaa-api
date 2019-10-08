<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../include/DbConnect.php';

$app = new \Slim\App;


$app->get('/', function (Request $request, Response $response, array $args) {
    
    $response->getBody()->write(" Hello world");

    $db = new DbConnect;

    if($db->connect() != null)
    {
       echo 'Connection Successfull';
    }

    return $response;
});




$app->run();