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

    private function getUserDiagrams($userId) {
       return $this->fetchCustom('getDiagrams.sql', [equality('user_id')], ['user_id' => $userId]);
    }

    public function deleteSolutionsOfUser($userId) {

        $diagramData = $this->getUserDiagrams($userId);
        $diagramData = $this->orderData($diagramData, [
            '_index' => 'diagram_id',
            'diagram_id' => 'id'
        ]);

        $ids = array_map(function($diagram){
            return $diagram['id'];
        }, $diagramData);

        if(count($ids) > 0) {
            $this->delete('diagram', [inOp('diagram_id', $ids)], $ids);
        }

        $this->delete('solution', [equality('user_id')], ['user_id' => $userId]);
    }

    public function removeReviewer($teacherId) {
        return $this->update('solution', 
            ['reviewed_by' => 'NULL'],
            [equality('reviewed_by', ':id')],
            ['id' => $teacherId]
        );
    }

    public function getSolutionsOfUser($userId) {
        $results = $this->fetchCustom('getFullSolutions.sql', 
        [equality('user_id')],
        ['user_id' => $userId]);

        return organizeFullSolution($results);
    }

    public function refreshUpdatedAt($solutionId) {
        $args = ['solution_id' => $solutionId];
        $this->update(
            'solution', 
            ['updated_at' => 'NOW()'], 
            [equality('solution_id')], 
            $args
        );

        $this->update(
            'solution',
            ['submitted_at' => 'NOW()'],
            [equality('solution_id'), isNotNull('submitted_at   ')],
            $args
        );
    }

    public function submit($solutionId, $plannedAssignmentId) {
        return $this->update(
            'solution',
            [
                'submitted_at' => 'NOW()',
                'planned_assign_id' => ':plannedAssign'
            ],
            [equality('solution_id')],
            [
                'plannedAssign' => $plannedAssignmentId,
                'solution_id' => $solutionId,
            ]);
    }

    public function unsubmitOthers($userId, $plannedAssignmentId) {
        return $this->update(
            'solution',
            [
                'submitted_at' => 'NULL',
                'planned_assign_id' => 'NULL'
            ],
            [
                equality('planned_assign_id', ':plannedAssign'),
                equality('user_id')
            ],
            [
                'plannedAssign' => $plannedAssignmentId,
                'user_id' => $userId
            ]);
    }

    public function unsubmit($solutionId) {
        return $this->update(
            'solution',
            [
                'submitted_at' => 'NULL',
                'planned_assign_id' => 'NULL'
            ],
            [
                equality('solution_id')
            ],
            [
                'solution_id' => $solutionId
            ]
        );
    }

    public function fetchReviewedSolutions($userId, $plannedAssignmentId = NULL) {
        $results = $this->fetchCustom('getFullSolutions.sql', 
        [
            equality('user_id'), 
            equalityOrNull('planned_assign_id'), 
            isNotNull('reviewed_by')
        ],
        [
            'user_id' => $userId,
            'planned_assign_id' => $plannedAssignmentId
        ]);
        
        return organizeFullSolution($results);
    }

    public function getSolutionReviewer($solutionId) {
        return $this->fetchOne('getReviewer.sql', ['id' => $solutionId]);
    }

    public function putMark($solutionId, $teacherId, $mark) {
        $params = [
            'mark' => $mark ? $mark : NULL,
            'reviewed_by' => $teacherId,
            'solution_id' => $solutionId
        ];

        if(!$mark) {
            unset($params['reviewed_by']);
        }
        return $this->update('solution', [
            'mark' => ':mark',
            'reviewed_at' => $mark ? 'NOW()' : 'NULL',
            'reviewed_by' => $mark ? ':reviewed_by': 'NULL'
        ],[
            equality('solution_id')
        ],
        $params
        );
    }

    public function getSolutionCount($userId) {
        return (int)$this->fetchOne('getSolutionCount.sql', ['user_id' => $userId])['count'];
    }

}