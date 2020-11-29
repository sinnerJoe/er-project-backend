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
    $userData = $user->findUserByEmail($body->email);

    if(!$userData || !password_verify($body->password, $userData["password"])) {
        $http->notAuthorized("Either the user account doesn't exist or the password is wrong.");
    }
    $http->ok(null, "Authentication succeeded.");
});

$router->handleGet(function ($http, $body) {
});


$router->run();
?>
