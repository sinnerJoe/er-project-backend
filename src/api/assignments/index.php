<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/assignment/index.php');

$router = new Router();

$router->handlePost(function ($http, $body) {
    $assignment = new Assignment();

    $assignment->createAssignment($body['title'], $body['description']);

    $http->ok();
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleGet(function ($http) {
    $assignment = new Assignment();
    
    if(isset($_GET['id'])) {
        $data = $assignment->getAssignment($_GET['id']);
    } else {
        $data = $assignment->getAllAssignments();
    }    
    
    $http->ok($data);
})->addValidator(is_authenticated);

$router->handlePut(function ($http, $body) {
    $assignment = new Assignment();

    $assignment->updateAssignment($_GET['id'], $body['title'], $body['description']);

    $http->ok();
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function ($http, $body) {
    $assignment = new Assignment();

    $assignment->deleteAssignment($_GET['id']);

    $http->ok();
})->addValidator(is_authenticated)->addValidator(is_teacher);

