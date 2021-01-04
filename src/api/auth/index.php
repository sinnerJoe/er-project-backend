<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/session.php');

$router = new Router(false);

$router->handlePost(function ($http, $body) {
    $user = new User();
    $userData = $user->findUserByEmail($body['email']);

    if(!$userData || !password_verify($body['password'], $userData["password"])) {
        $http->ok(false, "Either the user account doesn't exist or the password is wrong.");
    }

    if((int)$userData['disabled']) {
        $http->ok(false, "The account isn't active. The email address wasn't confirmed. You should check your spam inbox on you email address.");
    }

    registerSession($userData['user_id']);
    loginSession();
    $http->ok(true, "Authentication succeeded. ");
});

$router->handleGet(function ($http, $body) {
    $user = new User();
    loginSession();
    $sessionData = getSessionData();
    if($sessionData->authenticated) {
        $http->ok(array(
            'userId' => $sessionData->userId,
            'email' => $sessionData->email,
            'role' => $sessionData->role 
        ), "Login session exists.");
    }
    $http->ok(null, "Login session doesn't exist.");
});

$router->handleDelete(function ($http, $body) {
    $user = new User();
    logoutSession();
    $http->ok(null, "Session ended.");
});

?>
