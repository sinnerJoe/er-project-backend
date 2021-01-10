<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/assignment/index.php');

$router = new Router();

$router->handlePost(function ($http, $body) {
    $assignment = new Assignment();

    $assignment->createAssignment($body['title'], $body['description']);

    $http->ok();
})->addValidator(is_authenticated_strict)->addValidator(is_teacher);

$router->handleGet(function ($http) {
    $assignment = new Assignment();
    
    if(isset($_GET['id'])) {
        $data = $assignment->getAssignment($_GET['id']);
        if(!$data) {
            $http->notFound("Assignment couldn't be found.");
        }
    } else {
        $data = $assignment->getAllAssignments();
    }
    
    $http->ok($data);
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handlePut(function ($http, $body) {
    $assignment = new Assignment();

    $assignment->updateAssignment($_GET['id'], $body['title'], $body['description']);

    $http->ok();
})->addValidator(is_authenticated_strict)->addValidator(is_teacher);

$router->handleDelete(function ($http, $body) {
    $assignment = new Assignment();

    $id = $_GET['id'];
    
    $assignmentData = $assignment->getAssignment($id);

    if(!$assignmentData) {
        $http->notFound("The assignment you're trying to delete doesn't exist");
    }
    $educationalPlansCount = count($assignmentData['plannedAssignments']);
    if($educationalPlansCount > 0) {
        $http->badRequest("The assignment is scheduled in ".$educationalPlansCount." educational plan(s). Please remove it from the schedule before attempting to delete.");
    }

    $assignment->deleteAssignment($id);

    $http->ok("The assignment was deleted successfully.");
})->addValidator(is_authenticated_strict)->addValidator(is_teacher);

