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
paramenters: firstname, lastname, email, password, phone, region, state, block, building, floor, flat
method: post
*/
$app->post('/createuser', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('firstname','lastname', 'email', 'password', 'phone', 'region' , 'state','block', 'street','building', 'floor', 'flat' ), $request, $response))
    {
        $request_data = $request->getParsedBody();
        
        $firstname = $request_data['firstname'];
        $lastname = $request_data['lastname'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $phone = $request_data['phone'];
        $region = $request_data['region'];
        $state = $request_data['state'];
        $block = $request_data['block'];
        $street = $request_data['street'];
        $building = $request_data['building'];
        $floor = $request_data['floor'];
        $flat = $request_data['flat'];

        $hase_password = password_hash($password, PASSWORD_DEFAULT);

        $db = new DbOperations;

        $result = $db->createUser($firstname, $lastname, $email, $hase_password, $phone, $region, $state, $block, $street,$building, $floor, $flat);
        
        if($result == USER_CREATED)
        {
           $message = array();
           $message['error'] = false;
           $message['message'] = 'User created successfully';
           $response->write(json_encode($message));
           return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(200);

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
           $message['error'] = true;
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

/*
endpoint: login user
paramenters: email, password
method: post
*/

$app->post('/userlogin', function(Request $request, Response $response){
       
    if(!haveEmptyParameters(array('email', 'password'), $request, $response))
    {
        $request_data = $request->getParsedBody();
        
        $email = $request_data['email'];
        $password = $request_data['password'];

        $db = new DbOperations;
        $result = $db->userLogin($email, $password);

        if($result == USER_AUTHENTICATED)
        {
            $user = $db->getUserByEmail($email);

            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'Login Successful';
            $response_data['user'] = $user;

            $response->write(json_encode($response_data));
            return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(200);
        }
        elseif($result == USER_NOT_FOUND)
        {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'User not exist';
            
            $response->write(json_encode($response_data));
            return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);

        }
        elseif($result == USER_PASSWORD_DO_NOT_MATCH)
        {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Invalid credential';
            
            $response->write(json_encode($response_data));
            return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);

        }
    }
    return $response
                          ->withHeader('Content-type', 'application/json')
                          ->withStatus(422);
});



/*
endpoint: all users
paramenters: No
method: get
*/
$app->get('/allusers', function(Request $request, Response $response ){
        $db = new DbOperations;

        $users = $db->getAllUsers();

        $response_data['error'] = false;
        $response_data['users'] = $users;

        $response->write(json_encode($response_data));

        return $response->withHeader('Content-type', 'application/json')
        ->withStatus(200);

});


/*
endpoint: all regions 
paramenters: No
method: get
*/
$app->get('/allregion', function(Request $request, Response $response ){
    $db = new DbOperations;

    $regions = $db->getAllRagion();

    $response_data['error'] = false;
    $response_data['regions'] = $regions;

    $response->write(json_encode($response_data));

    return $response->withHeader('Content-type', 'application/json')
    ->withStatus(200);

});



/*
endpoint: update user 
paramenters: phone block street building floor flat
method: put
*/
$app->put('/updateuser/{id}', function(Request $request, Response $response, array $args){

    $id = $args['id'];

    if(!haveEmptyParameters(array('email','phone', 'region', 'state', 'block', 'street', 'building', 'floor', 'flat', 'id'), $request, $response))
    {
        $request_data = $request->getParsedBody();


        $email = $request_data['email'];
        $phone = $request_data['phone'];
        $region = $request_data['region'];
        $state = $request_data['state'];
        $block = $request_data['block'];
        $street = $request_data['street'];
        $building = $request_data['building'];
        $floor = $request_data['floor'];
        $flat = $request_data['flat'];
        $id = $request_data['id'];

        $db = new DbOperations;

        if($db->updateUser($email, $phone, $region, $state, $block, $street, $building, $floor, $flat, $id))
        {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'User Update Syccessfullt';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user;

            $response->write(json_encode($response_data));
            return $response
                           ->withHeader('Content-type', 'application/json')
                           ->withStatus(200);
            
        }
        else
        {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Please try agine later';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user;

            $response->write(json_encode($response_data));
            return $response
                           ->withHeader('Content-type', 'application/json')
                           ->withStatus(442);
        }
    }   
    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
});



/*
endpoint: change user password 
paramenters: current password   new password   email
method: put
*/
$app->put('/updatepassword', function(Request $request, Response $response, array $args){



    if(!haveEmptyParameters(array('currnetpassword', 'newpassword', 'email'), $request, $response))
    {
      $request_data = $request->getParsedBody();

      $currnetpassword = $request_data['currnetpassword'];
      $newpassword = $request_data['newpassword'];
      $email = $request_data['email'];

      $db = new DbOperations;

      $result = $db->updatePassword($currnetpassword, $newpassword, $email);

       if($result == PASSWORD_CHANGED)
       {
        $response_data = array();
        $response_data['error'] = false;
        $response_data['message'] = 'Password Changed';

        $response->write(json_encode($response_data));

        return $response
                       ->withHeader('Content-type', 'application/json')
                       ->withStatus(200);

       }
       elseif($result == PASSWORD_DO_NOT_MATCH)
       {
        $response_data = array();
        $response_data['error'] = true;
        $response_data['message'] = 'You have given wrong password';

        $response->write(json_encode($response_data));
        
        return $response
                       ->withHeader('Content-type', 'application/json')
                       ->withStatus(422);

       }
       elseif($result == PASSWORD_NOT_CHANGED)
       {
        $response_data = array();
        $response_data['error'] = true;
        $response_data['message'] = 'Some error Occurred';

        $response->write(json_encode($response_data));
        
        return $response
                       ->withHeader('Content-type', 'application/json')
                       ->withStatus(422);
       }

    }

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);


});


/*
endpoint: delete user  
paramenters: id
method: delete
*/
$app->delete('/deleteuser/{id}', function(Request $request, Response $response, array $args){

      $id = $args['id'];
      $db = new DbOperations;

      if($db->deleteUser($id))
      {
        $response_data = array();
        $response_data['error'] = false;
        $response_data['message'] = 'User has been deleted';

        $response->write(json_encode($response_data));

        return $response
                       ->withHeader('Content-type', 'application/json')
                       ->withStatus(200); 
      }
      else
      {
        $response_data = array();
        $response_data['error'] = true;
        $response_data['message'] = 'Plase try agine later';

        $response->write(json_encode($response_data));

        return $response
                       ->withHeader('Content-type', 'application/json')
                       ->withStatus(422);

      }

    
    

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);


});


//This function to check parameter if empty or not
function haveEmptyParameters($required_params, $request, $response){

    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();

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