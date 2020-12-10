<?php
require_once(__DIR__.'/../../../models/plan/index.php');
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
