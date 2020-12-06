<?php
require_once(__DIR__."/../Model.php");
require_once(__DIR__."/../image/index.php");
require_once(__DIR__.'/hierarchy.php');

class Solution extends Model {
    public function __construct(){
        parent::__construct(__DIR__);
    }
    
    public function createSolution($solutionData) {
       return $this->insert('createSolution.sql', $solutionData)->lastInsertId();
    }

    public function createDiagram($diagramData) {
        return $this->insert('createDiagram.sql', $diagramData);
    }

    public function getSolutionById($solutionId) {
        $results =  $this->fetchAll('getSolutionById.sql', ['solution_id' => $solutionId]);

        return organizePartialSolution($results)[0];
    }

    public function getDiagramById($diagramId) {
        return $this->fetchOne('getDiagramById.sql', ['diagram_id' => $diagramId]);
    }

    public function getFullSolutionById($solutionId) {
        $data = $this->fetchCustom('getFullSolutions.sql', 
        [equality('solution_id')],
        ['solution_id' => $solutionId]);

        return organizeFullSolution($data)[0];
    }

    public function deleteDiagram($diagramId) {
        return $this->execute('deleteDiagram.sql', ['diagram_id' => $diagramId]);
    }

    public function deleteSolution($solutionId) {
        return $this->execute('deleteSolution.sql', ['solution_id' => $solutionId]);
    }

    public function getSolutionsOfUser($userId) {
        $results = $this->fetchCustom('getFullSolutions.sql', 
        [equality('user_id')],
        ['user_id' => $userId]);

        return organizeFullSolution($results);
    }

    public function refreshUpdatedAt($solutionId) {
        return $this->patch(
            'solution', 
            ['updated_at' => 'NOW()'], 
            [equality('solution_id')], 
            ['solution_id' => $solutionId]
        );
    }
}