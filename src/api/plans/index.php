<?php
require_once(__DIR__.'/../../models/plan/index.php');
require_once(__DIR__.'/../../http/Router.php');
$router = new Router();

$router->handlePost(function($http, $body) {
   $plan = new Plan();
   $id = $plan->createPlan($body['name']);
   $http->ok(["id" => $id]);
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleGet(function($http, $body) {
    $plan = new Plan();

    $data = $plan->fetchAllPlans();
    $http->ok($data);
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $plan = new Plan();

    $planObj = $plan->fetchPlanById($_GET['id']);
    if(!isset($planObj)) {
        $http->notFound();
    }

    foreach($planObj['plannedAssignments'] as $plannedAssignment) {
        $plan->deletePlannedAssignment($plannedAssignment['id']);
    }

    $plan->deletePlan($_GET['id']);

    $http->ok();

})->addValidator(is_authenticated);