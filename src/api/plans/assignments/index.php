<?php
require_once(__DIR__.'/../../../models/plan/index.php');
require_once(__DIR__.'/../../../models/assignment/index.php');
require_once(__DIR__.'/../../../http/Router.php');
$router = new Router();

$router->handlePost(function($http, $body) {
    $plan = new Plan();
    foreach($body['assignments'] as $plannedAssignment) {
        $plan->createPlannedAssignment(
            $_GET['id'], 
            $plannedAssignment['startDate'], 
            $plannedAssignment['endDate'], 
            $plannedAssignment['assignment']['id']
        );
    }

    $http->ok();
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handlePut(function($http, $body) {
    $plan = new Plan();
    foreach($body['assignments'] as $assignment) {
        $plan->updatePlannedAssignment($assignment['id'], $assignment['startDate'], $assignment['endDate']);
    }
    $http->ok();
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $plan = new Plan();

    foreach($_GET['ids'] as $id) {
        $plan->deletePlannedAssignment($id);
    }

    $http->ok();

})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleGet(function($http, $body) {
    $assignment = new Assignment();
    $sessionData = getSessionData();
    $data = $assignment->getAllStudentAssignments($sessionData->userId);
    $http->ok($data);
})->addValidator(is_authenticated);