<?php


require_once(__DIR__."/../../models/solution/index.php");
require_once(__DIR__."/../../models/image/index.php");
require_once(__DIR__."/../../validators/auth.php");
require_once(__DIR__."/../../http/Router.php");

$router = new Router();


function removeDiagram($diagramId) {
    $image = new Image();
    $solution = new Solution();

    $diagram = $solution->getDiagramById($diagramId);

    if(!$diagram) {
        return;
    }

    $image->deleteImage($diagram['image_id'], $diagram['filepath']);

    $solution->deleteDiagram($diagramId);
}

function removeSolutionDiagrams($solutionId) {
    $sessionData = getSessionData();
    $solution = new Solution();
    $solutionData = $solution->getSolutionById($solutionId);

    $http = new HttpResponse();
    if(!$solutionData) {
        $http->notFound();
    }

    if($sessionData->userId !== $solutionData['userId'] && $sessionData->role !== 0) {
        $http->notAuthorized();
    }

    foreach($solutionData['diagrams'] as $diagram) {
        removeDiagram($diagram['id']);
    }
}

function saveDiagrams($diagrams, $solutionId) {

    $sessionData = getSessionData();
    $solution = new Solution();
    $image = new Image();

    foreach($diagrams as $diagram) {
        $imageId = $image->saveImage($diagram['image'], $sessionData->userId);
        $diagramData = [
            'name' => $diagram['name'],
            'content' => $diagram['content'],
            'solution_id' => $solutionId,
            'image_id' => $imageId,
            'type' => $diagram['type']
        ];
        $solution->createDiagram($diagramData);
    }
}

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
    removeSolutionDiagrams($body['id']);

    $solution = new Solution();
    
    $solution->deleteSolution($body['id']);

    $http->ok(null, "Solution successfully deleted.");

})->addValidator(is_authenticated);



$router->handlePut(function($http, $body) {
    $sessionData = getSessionData();
    $solution = new Solution();

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

function checkCanStatusBeChanged($plannedAssignmentId) {
    $solution = new Solution();
    $sessionData = getSessionData();
    $solutionData = $solution->getSolutionById($_GET['id']);
    if(!$solutionData) {
        $http->notFound('Solution not found');
    }
    if($solutionData['userId'] !== $sessionData->userId) {
        $http->notAuthorized("You aren't the owner of the solution.");
    }
    if($solutionData['reviewedAt'] !== null) {
        $http->notAuthorized("This solution was already reviewed, you cannot resubmit it");
    }

    $reviewdSolutions = $solution->fetchReviewedSolutions($sessionData->userId, $plannedAssignmentId);
    if(count($reviewdSolutions) > 0) {
        $http->notAuthorized("Your solution to this assignment was already reviewed. You cannot change it anymore.");
    }
}

$router->handlePatch(function($http, $body) {
    $solution = new Solution();
    $sessionData = getSessionData();
    $plannedAssignmentId = $body['plannedAssignmentId'];

        if($_GET['target'] === 'submit') {
            
            checkCanStatusBeChanged($plannedAssignmentId);

            $solution->unsubmitOthers($sessionData->userId, $plannedAssignmentId);
            $solution->submit($_GET['id'], $plannedAssignmentId);
            $http->ok();
        } else if($_GET['target'] === 'unsubmit') {
            checkCanStatusBeChanged($plannedAssignmentId);
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