<?php
require_once(__DIR__.'/../../models/group/index.php');
require_once(__DIR__.'/../../http/Router.php');
$router = new Router();

$router->handleGet(function($http, $body) {
    $group = new Group();
    if($_GET['type'] === 'shallow') {
       $http->ok($group->getShallowGroups($_GET['year'])); 
    } else if($_GET['type'] === 'submissions') {
       $http->ok($group->getSubmissionGroups($_GET['year']));
    }
    is_authenticated($http, $body);
    $http->ok($group->getGroups($_GET['year']));
    
});

$router->handlePost(function($http, $body) {
    $group = new Group();

    $group->createGroup($body['year'], $body['name']);

    $http->ok();

})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $group = new Group();

    $group->deleteGroup($_GET['id']);

    $http->ok();
})->addValidator(is_authenticated);

$router->handlePatch(function($http, $body) {
    $group = new Group();
    if($_GET['target'] === 'coordinator') {
        $group->changeCoordinator($_GET['id'], $body['coordinatorId']);
        $http->ok();
    } else if($_GET['target'] === 'plan') {
        $group->changePlan($_GET['id'], $body['planId']);
        $http->ok(); 
    }
})->addValidator(is_authenticated)->addValidator(is_teacher); // change to admin