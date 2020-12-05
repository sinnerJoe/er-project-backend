<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');

$router = new Router(false);

$user = new User();

$router->handlePost(function ($http, $body) {
    // echo $_POST;
    global $user;
    // var_dump ((array)$body);
    // exit();
    
    $data = array( 
        'password' => password_hash($body['password'], PASSWORD_BCRYPT),
        'email' => $body['email'],
        'last_name' => $body['last_name'],
        'first_name' => $body['first_name'],
        // TODO: put college group back
        // 'college_group' => $body['college_group']
  ); 
    try {
        $user->register($data);
    } catch(Exception $e) {
        $http->badRequest("The user is already registered.".$e->getMessage());
    }
    $http->ok(null, "You registerd successfully.");
});

$router->handleGet(function ($http) {
    global $user;

    
});



?>