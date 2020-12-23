<?php

require_once(__DIR__."/../Model.php");

function addImageToDiagram(&$diagram) {
    $diagram['image'] = Image::getImageUrl($diagram['image']);
}

function addImageToSolutions(&$solutions) {
    foreach($solutions as &$solution) {
        foreach($solution['diagrams'] as &$diagram) {
            addImageToDiagram($diagram);
        }
    }
}

function organizeFullSolution($data) {
    $obj = new DataHierarchy([              
            '_index' => 'solution_id',
            'solution_id' => 'id',
            'user_id' => 'userId',
            'planned_assign_id' => 'plannedAssignmentId',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
            'mark' => 'mark',
            'reviewed_by' => 'reviewedBy',
            'reviewed_at' => 'reviewedAt',
            'title' => 'title',
            'diagrams' => [
                '_index' => 'diagram_id',
                'diagram_id' => 'id',
                'name' => 'name',
                'type' => 'type',
                'content' => 'content',
                'filepath' => 'image'
            ],
        ]);
    
    $solutions = $obj->orderData($data);

    addImageToSolutions($solutions);

    return $solutions;
}

function organizePartialSolution($data) {
   $obj =  new DataHierarchy([
            '_index' => 'solution_id',
            'solution_id' => 'id',
            'user_id' => 'userId',
            'planned_assign_id' => 'plannedAssignmentId',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
            'mark' => 'mark',
            'reviewed_by' => 'reviewedBy',
            'reviewed_at' => 'reviewedAt',
            'title' => 'title',
            'diagrams' => [
                '_index' => 'diagram_id',
                'diagram_id' => 'id',
                'type' => 'type'
            ],
        ]
   );

   return $obj->orderData($data);
}