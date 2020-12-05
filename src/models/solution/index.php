<?php
require_once(__DIR__."/../Model.php");
require_once(__DIR__."/../../utils/utils.php");

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

        if(count($results) === 0) {
            return NULL;
        }

        $data = Model::pick(results[0], [
            'title',
            'solution_id',
            'user_id',
            'planned_assign_id',
            'created_at',
            'updated_at',
            'mark',
            'reviewed_at'
        ]);

        $data['diagrams'] = array_map(function($row) {
            return $row['diagram_id'];
        });

        return $data;
    }

    public function getDiagramById($diagramId) {
        return $this->fetchOne('getDiagramById.sql', ['diagram_id' => $diagramId]);
    }

    public function deleteDiagram($diagramId) {
        return $this->execute('deleteDiagram.sql', ['diagram_id' => $diagramId]);
    }

    public function deleteSolution($solutionId) {
        return $this->execute('deleteSolution.sql', ['solution_id' => $solutionId]);
    }

    public function getSolutionsOfUser($userId) {
        $results = $this->fetchAll('getSolutionsOfUser.sql', [
            'user_id' => $userId
        ]);

        if(!$results || !count($results)) {
            return [];
        }


        // var_dump($results);

        // return Model::accumulate_ordered($results, 'solution_id', 
        // function($row) {
        //     $solution = Model::pick($row, [
        //         'solution_id',
        //         'user_id',
        //         'planned_assign_id',
        //         'created_at',
        //         'updated_at',
        //         'mark',
        //         'reviewed_by',
        //         'reviewed_at',
        //         'title'
        //     ]);
        //     $solution['diagrams'] = [];
        //     return $solution;
        // },
        // function($solution, $row) {
        //     array_push(
        //         $solution['diagrams'], 
        //         Model::pick($row, [
        //             'diagram_id',
        //             'name',
        //             'content',
        //             'filepath'
        //         ])
        //     );
        //     return $solution;
        // });

        $obj = new DataHierarchy('solution_id', [              
                'solution_id' => 'solutionId',
                'user_id' => 'userId',
                'planned_assign_id' => 'plannedAssignmentId',
                'created_at' => 'createdAt',
                'updated_at' => 'updatedAt',
                'mark' => 'mark',
                'reviewed_by' => 'reviewedBy',
                'reviewed_at' => 'reviewedAt',
                'title' => 'title'
            ]);
            return $obj->addLayer('diagram_id', 'diagrams', [
                'diagram_id' => 'diagramId',
                'name' => 'name',
                'content' => 'content',
                'filepath' => 'image'
            ])->orderData($results);
        

    }
}