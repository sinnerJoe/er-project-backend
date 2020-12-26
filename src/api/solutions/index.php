<?php


require_once(__DIR__."/../../models/solution/index.php");
require_once(__DIR__."/../../models/image/index.php");
require_once(__DIR__."/../../validators/auth.php");
require_once(__DIR__."/../../http/Router.php");
require_once(__DIR__."/services.php");

$router = new Router();



$router->handlePost(function($http, $body) {
    $sessionData = getSessionData();

    $solutionData = [
        "user_id" => $sessionData->userId,
        "title" => $body['title']
    ];

    $solution = new Solution();
    $image = new Image();

    $solutionId = $solution->createSolution($solutionData);

    saveDiagrams($body['diagrams'], $solutionId);

    $http->ok(null, "Solution created successfully");
})->addValidator(is_authenticated);



$router->handleDelete(function($http, $body) {
    $sessionData = getSessionData();
    
    checkCanEditSolution($body['id']);
    removeSolutionDiagrams($body['id']);

    $solution = new Solution();
    
    $solution->deleteSolution($body['id']);

    $http->ok(null, "Solution successfully deleted.");

})->addValidator(is_authenticated);



$router->handlePut(function($http, $body) {
    $sessionData = getSessionData();
    $solution = new Solution();

    checkCanEditSolution($_GET['id']);

    removeSolutionDiagrams($_GET['id']);

    saveDiagrams($body['diagrams'], $_GET['id']);

    $solution->refreshUpdatedAt($_GET['id']);
    $http->ok(null, "Solution successfully modified.");
})->addValidator(is_authenticated);

$router->handleGet(function($http, $body) {
    $sessionData = getSessionData();
    $solution = new Solution();

    if(isset($_GET['id'])) {
        $solutionData = $solution->getFullSolutionById($_GET['id']);
        if(!$solutionData) {
            $http->notFound();
        }
        if($solutionData['userId'] != $sessionData->userId && $sessionData->role === 10) {
            $http->notAuthorized();
        }
        $http->ok($solutionData);
    }

    $solutions = $solution->getSolutionsOfUser($sessionData->userId);

    $http->ok($solutions);
})->addValidator(is_authenticated);


$router->handlePatch(function($http, $body) {
    $solution = new Solution();
    $sessionData = getSessionData();
    $plannedAssignmentId = $body['plannedAssignmentId'];

        if($_GET['target'] === 'submit') {
            
            checkCanStatusBeChanged($plannedAssignmentId, $http);

            $solution->unsubmitOthers($sessionData->userId, $plannedAssignmentId);
            $solution->submit($_GET['id'], $plannedAssignmentId);
            $http->ok();
        } else if($_GET['target'] === 'unsubmit') {
            checkCanStatusBeChanged($plannedAssignmentId, $http);
            $solution->unsubmit($_GET['id']);

            $http->ok();
        } else if($_GET['target'] === 'mark') {
            is_teacher();
            $solutionId = $_GET['id'];
            if(!$sessionData->isAdmin) {
                $reviewerData = $solution->getSolutionReviewer($solutionId);
                if(!$reviewerData || $reviewerData['user_id'] != $sessionData->userId) {
                    $http->notAuthorized('You cannot grade groups that you\'re not responsible of');
                }
            }
            $solution->putMark($solutionId, $sessionData->userId, $body['mark']);
            $http->ok('Mark assigned successfully.');
        }
})->addValidator(is_authenticated);


?>