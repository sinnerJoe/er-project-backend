<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/session.php');

$router = new Router(false);

$router->handlePost(function ($http, $body) {
    $user = new User();
    $userData = $user->findUserByEmail($body['email']);

    if(!$userData || !password_verify($body['password'], $userData["password"])) {
        $http->notAuthenticated("Either the user account doesn't exist or the password is wrong.");
    }
    registerSession($userData['user_id']);
    loginSession();
    $http->ok(null, "Authentication succeeded. ".session_id());
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
