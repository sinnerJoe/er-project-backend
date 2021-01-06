<?php
require_once(__DIR__.'/../../models/plan/index.php');
require_once(__DIR__.'/../../models/group/index.php');
require_once(__DIR__.'/../../models/solution/index.php');
require_once(__DIR__.'/../../http/Router.php');
$router = new Router();

$router->handlePost(function($http, $body) {
   $plan = new Plan();
   $id = $plan->createPlan($body['name']);
   $http->ok(["id" => $id]);
})->addValidator(is_authenticated_strict)->addValidator(is_teacher);

$router->handleGet(function($http, $body) {
    $plan = new Plan();

    if(isset($_GET['id'])) {
        $data = $plan->fetchPlanById($_GET['id']);
    } else {
        $data = $plan->fetchAllPlans();
    }

    $http->ok($data);
})->addValidator(is_authenticated)->addValidator(is_teacher);

$router->handleDelete(function($http, $body) {
    $plan = new Plan();
    $group = new Group();
    $solution = new Solution();

    $planId = $_GET['id'];

    $planObj = $plan->fetchPlanById($planId);
    if(!isset($planObj)) {
        $http->notFound();
    }

    foreach($planObj['plannedAssignments'] as $plannedAssignment) {
        $solution->removeSubmissionsToAssignment($plannedAssignment['id']);
        $plan->deletePlannedAssignment($plannedAssignment['id']);
    }

    $group->clearPlan($planId);
    $plan->deletePlan($planId);

    $http->ok();

})->addValidator(is_authenticated_strict);

$router->handlePatch(function($http, $body) {
    $plan = new Plan();

    $plan->updatePlanName($_GET['id'], $body['name']);

    $http->ok();
})->addValidator(is_authenticated_strict)->addValidator(is_teacher);