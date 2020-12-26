<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/image/index.php');
require_once(__DIR__.'/../../models/solution/index.php');
require_once(__DIR__.'/../../models/group/index.php');

$router = new Router(false);


$router->handlePost(function ($http, $body) {
    // echo $_POST;
    $user = new User();
    // var_dump ((array)$body);
    // exit();
    
    $data = array( 
        'password' => password_hash($body['password'], PASSWORD_BCRYPT),
        'email' => $body['email'],
        'last_name' => $body['last_name'],
        'first_name' => $body['first_name'],
        'college_group_id' => $body['college_group']
  ); 
    try {
        $user->register($data);
    } catch(Exception $e) {
        $http->badRequest("The user is already registered.".$e->getMessage());
    }
    $http->ok(null, "You registerd successfully.");
});

$router->handleGet(function ($http, $body) {
    $user = new User();
    
    if($_GET['role'] === 'teacher') {
        $http->ok($user->getTeachers($_GET['year'])); 
    }

    if($_GET['role'] === 'student') {
        $http->ok($user->fetchByRole(10));
    }
    $http->ok($user->fetchByRegistrationYear($_GET['year']));
});

$router->handlePatch(function($http, $body) {
    $user = new User();

    $sessionData = getSessionData();

    if($_GET['target'] === 'group') {
        $user->changeGroup($_GET['id'], $body['groupId']);
        $http->ok();
    }
    if($_GET['target'] === 'role') {
        if(!$sessionData->isAdmin) {
            $http->notAuthorized("You have to be an admin to change roles.");
        }
        $user->changeRole($_GET['id'], $body['role']);
        $http->ok();
    }
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $sessionData = getSessionData();
    $deletedUserId = $sessionData->userId;
    if($_GET['id']) {
        if(!$sessionData->isAdmin) {
            $http->notAuthorized("You need to be an admin to delete user accounts");
        } 
        $deletedUserId = $_GET['id'];
    }

    $image = new Image();
    $solution = new Solution();
    $user= new User();
    $group = new Group();

    $image->deleteImagesOfUser($deletedUserId);
    $solution->deleteSolutionsOfUser($deletedUserId);
    $solution->removeReviewer($deletedUserId);
    $group->removeCoordinator($deletedUserId);
    $user->deleteUser($deletedUserId);

    
    $http->ok("User successfully delete.");

})->addValidator(is_authenticated);


?>