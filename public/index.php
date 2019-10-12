<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../include/DbOperations.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,

    ]
];

$app = new \Slim\App($config);

/*
endpoint: createuser
paramenters: firstname, lastname, email, password, phone, block, building, floor, flat
method: post
*/


$app->post('/createuser', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('firstname','lastname', 'email', 'password', 'phone', 'block', 'street','building', 'floor', 'flat' ), $response))
    {
        $request_data = $request->getParsedBody();
        
        $firstname = $request_data['firstname'];
        $lastname = $request_data['lastname'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $phone = $request_data['phone'];
        $block = $request_data['block'];
        $street = $request_data['street'];
        $building = $request_data['building'];
        $floor = $request_data['floor'];
        $flat = $request_data['flat'];

        $hase_password = password_hash($password, PASSWORD_DEFAULT);

        $db = new DbOperations;

        $result = $db->createUser($firstname, $lastname, $email, $hase_password, $phone, $block, $street,$building, $floor, $flat);
        
        if($result == USER_CREATED)
        {
           $message = array();
           $message['error'] = false;
           $message['message'] = 'User created successfully';
           $response->write(json_encode($message));
           return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(201);

        }
        else if($result == USER_FAILURE)
        {
            $message = array();
           $message['error'] = true;
           $message['message'] = 'Some error occurred';
           $response->write(json_encode($message));
           return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);

        }
        else if($result == USER_EXISTS)
        {

            $message = array();
           $message['error'] = false;
           $message['message'] = 'User Already Exists';
           $response->write(json_encode($message));
           return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);

        }
        return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);


    }


});

function haveEmptyParameters($required_params, $response){

    $error = false;
    $error_params = '';
    $request_params = $_REQUEST;

    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param]) <= 0)
        {
            $error = true;
            $error_params .= $param . ', ';

        }
    }

    if($error)
    {
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error;
}





$app->run();