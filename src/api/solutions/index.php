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

    if($sessionData->userId !== $solutionData['user_id'] && $sessionData->role !== 0) {
        $http->notAuthorized();
    }

    foreach($solutionData['diagrams'] as $diagramId) {
        removeDiagram($diagramId);
    }
}

function saveDiagrams($diagrams, $solutionId) {

    $sessionData = getSessionData();
    $solution = new Solution();

    foreach($diagrams as $diagram) {
        $imageId = $image->saveImage($diagram['image'], $sessionData->userId);
        $diagramData = [
            'name' => $diagram['name'],
            'content' => $diagram['content'],
            'solution_id' => $solutionId,
            'image_id' => $imageId
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

    removeSolutionDiagrams($body['id']);

    saveDiagrams($body['diagrams'], $body['id']);


    $http->ok(null, "Solution successfully modified.");
})->addValidator(is_authenticated);

$router->handleGet(function($http, $body) {
    $sessionData = getSessionData();
    $solution = new Solution();

    $solutions = $solution->getSolutionsOfUser($sessionData->userId);

    $http->ok($solutions);
})->addValidator(is_authenticated);


?>