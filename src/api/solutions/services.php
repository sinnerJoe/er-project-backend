<?PHP
require_once(__DIR__."/../../models/solution/index.php");
require_once(__DIR__."/../../models/image/index.php");
require_once(__DIR__."/../../validators/auth.php");
require_once(__DIR__."/../../http/Router.php");

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

function checkCanStatusBeChanged($plannedAssignmentId, $http) {
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

function checkCanEditSolution($solutionId) {
    $http = new HttpResponse();
    $sessionData = getSessionData();
    $solution = new Solution();
    $data = $solution->getFullSolutionById($solutionId);
    if(!$data) {
        $http->notFound('Solution doesn\'t exist');
    }
    if($data['userId'] !== $sessionData->userId && !$sessionData->isAdmin) {
        $http->notAuthorized('You can\'t edit nor delete solutions of other users');
    }
    if($data['assignment']) {
        $http->badRequest("Cannot edit nor delete submitted solutions. If the solution is not evaluated, you can unsubmit it before editing/deleting.");
    }
}