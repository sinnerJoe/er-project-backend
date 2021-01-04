<?php
require_once(__DIR__.'/../../models/group/index.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/solution/index.php');
require_once(__DIR__.'/../../http/Router.php');

$router = new Router();

$router->handleGet(function($http, $body) {
    $group = new Group();
    if($_GET['type'] === 'shallow') {
       $http->ok($group->getShallowGroups($_GET['year'])); 
    } 
    
    is_authenticated($http, $body);
    is_teacher($http, $body);
    if($_GET['type'] === 'submissions') {
       $http->ok($group->getSubmissionGroups($_GET['year']));
    } else {
        $http->ok($group->getGroups($_GET['year']));
    }
    
});

$router->handlePost(function($http, $body) {
    $group = new Group();

    $group->createGroup($body['year'], $body['name']);

    $http->ok();

})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $group = new Group();
    $user = new User();
    $solution = new Solution();

    $groupId = $_GET['id'];
    $members = $user->getGroupMemberIds($groupId);
    $solution->removeSubmissionOfUsers($members);
    $user->removeMembersFromGroup($groupId);

    $group->deleteGroup($groupId);

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