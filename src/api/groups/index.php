<?php
require_once(__DIR__.'/../../models/group/index.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/solution/index.php');
require_once(__DIR__.'/../../http/Router.php');

$router = new Router();

$router->handleGet(function($http, $body) {
    $group = new Group();
    $sessionData = getSessionData();
    if($_GET['type'] === 'shallow') {
       $http->ok($group->getShallowGroups($_GET['year'])); 
    } 
    
    is_authenticated($http, $body);
    is_teacher($http, $body);
    if($_GET['type'] === 'submissions') {
       $http->ok(
           $group->getSubmissionGroups($_GET['year'], $sessionData->isAdmin ? NULL : $sessionData->userId)
       );
    } else {
        $http->ok($group->getGroups($_GET['year']));
    }
    
});

$router->handlePost(function($http, $body) {
    $group = new Group();

    if(isset($_GET['copyTo'])) {
        $year = (int) $_GET['copyTo'];
        $groups = $group->getGroups($year - 1);
        if(count($groups) === 0) {
            $http->notFound("There are no groups created for ".($year - 1));
        }
        foreach($groups as $groupObj) {
            $group->createFullGroup(
                $year,
                $groupObj['name'], 
                $groupObj['plan']['id'], 
                $groupObj['coordinator']['id']
            );
        }
        $http->ok(NULL, count($groups)." groups were copied from ".($year - 1));
    } else {
        $group->createGroup($body['year'], $body['name']);
        $http->ok(NULL, "Group ".$body['name']." created successfully");
    }


})->addValidator(is_authenticated_strict)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $group = new Group();
    $user = new User();
    $solution = new Solution();

    $groupId = $_GET['id'];
    $members = $user->getGroupMemberIds($groupId);
    $solution->removeSubmissionOfUsers($members);
    $user->removeMembersFromGroup($groupId);

    $group->deleteGroup($groupId);

    $http->ok(NULL, "The group was successfully deleted.");
})->addValidator(is_authenticated_strict);

$router->handlePatch(function($http, $body) {
    $group = new Group();
    if($_GET['target'] === 'coordinator') {
        $group->changeCoordinator($_GET['id'], $body['coordinatorId']);
        $http->ok(NULL, "The coordinator was successfully set");
    } else if($_GET['target'] === 'plan') {
        $group->changePlan($_GET['id'], $body['planId']);
        $http->ok(NULL, "The educational plan for the group was changed successfully."); 
    }
})->addValidator(is_authenticated_strict)->addValidator(is_teacher); // change to admin