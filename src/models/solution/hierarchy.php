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

function organizeFullSolution($data, $excludeContent = FALSE) {
    $diagramSchema = [
                '_index' => 'diagram_id',
                'diagram_id' => 'id',
                'name' => 'name',
                'type' => 'type',
                'content' => 'content',
                'filepath' => 'image'
    ];

    if($excludeContent) {
        unset($diagramSchema['content']);
    }


    $obj = new DataHierarchy([              
            '_index' => 'solution_id',
            'solution_id' => 'id',
            'user_id' => 'userId',
            'planned_assign_id' => 'plannedAssignmentId',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
            'mark' => 'mark',
            'reviewed_at' => 'reviewedAt',
            'title' => 'title',
            'reviewer' => [
                '_index' => 'reviewed_by',
                '_single' => TRUE,
                'reviewed_by' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName'
            ],
            'assignment' => [
                '_index' => 'assign_id',
                '_single' => TRUE,
                'assign_id' => 'id',
                'a_title' => 'title'
            ],
            'diagrams' => $diagramSchema,
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