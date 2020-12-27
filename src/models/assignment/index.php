<?php

require_once(__DIR__."/../Model.php");
require_once(__DIR__."/../image/index.php");

class Assignment extends Model {
    public function __construct() {
        parent::__construct(__DIR__);
    }

    public function createAssignment($title, $description) {
        return $this->create('assign', [
            'title' => ':title',
            'description' => ':description'
        ],[
            'title' => $title,
            'description' => $description
        ]);
    }

    public function updateAssignment($id, $title, $description) {
        return $this->update('assign', [
            'updated_at' => 'NOW()',
            'title' => ':title',
            'description' => ':description',
        ],
            [equality('assign_id')],
            [
                'assign_id' => $id,
                'title' => $title,
                'description' => $description
            ]
        );
    }

    private function orderAssignmentData($assignments) {
        return $this->orderData($assignments, [
            '_index' => 'assign_id',
            'assign_id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'updated_at' => 'updatedAt'
        ]);
    }

    public function getAssignment($id) {
        $data = $this->fetchCustom('getAssignments.sql',
            [equality('assign_id')],
            ['assign_id' => $id]
        );

        return $this->orderAssignmentData($data)[0];
    }

    public function getAllAssignments() {
        $data = $this->fetchAll("getAssignments.sql");
        return $this->orderAssignmentData($data);
    }

    public function deleteAssignment($id) {
        return $this->delete('assign', [equality('assign_id')], ['assign_id' => $id]);
    }

    public function getAllStudentAssignments($studentId) {
       $data = $this->fetchCustom('getFullAssignments.sql', 
       [equality('student_id')], 
       ['student_id' => $studentId]
    );

        return $this->orderData($data, [
            '_index' => 'planned_assign_id',
            'planned_assign_id' => 'id',
            'start_date' => 'startDate',
            'end_date' => 'endDate',
            'assignment' => [
                '_index' => 'assign_id',
                '_single' => TRUE,
                'assign_id' => 'id',
                'title' => 'title',
                'description' => 'description'
            ],
            'solution' => [
                '_index' => 'solution_id',
                '_single' => TRUE,
                'solution_id' => 'id',
                'solution_title' => 'title',
                'submitted_at' => 'submittedAt',
                'created_at' => 'createdAt',
                'updated_at' => 'updatedAt',
                'mark' => 'mark',
                'reviewed_at' => 'reviewedAt'
            ],
            'reviewer' => [
                '_index' => 'teacher_id',
                '_single' => TRUE,
                'teacher_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName'
            ]
        ]);

    }

    public function getPlannedAssignmentsForEvaluation($groupId, $plannedAssignmentId = NULL) {
        $data = $this->fetchCustom('getAssignmentsWithSolutions.sql',
            [
                equality('college_group_id'),
                equalityOrNull('planned_assign_id')
            ],
            [
                'college_group_id' => $groupId,
                'planned_assign_id' => $plannedAssignmentId
            ]
        );
        
        foreach($data as $key => $row) {
            if($row['image']) {
                $data[$key]['image'] = Image::getImageUrl($row['image']);
            }
        };

        return $this->orderData($data, [
            '_index' => 'planned_assign_id',
            'planned_assign_id' => 'id',
            'start_date' => 'startDate',
            'end_date' => 'endDate',
            'assignment' => [
                '_index' => 'assign_id',
                '_single' => TRUE,
                'assign_id' => 'id',
                'title' => 'title',
                'description' => 'description'
            ],
            'students' => [
                '_index' => 'student_id',
                'student_id' => 'id',
                'email' => 'email',
                'student_first_name' => 'firstName',
                'student_last_name' => 'lastName',
                'solution' => [
                    '_index' => 'solution_id',
                    '_single' => TRUE,
                    'solution_id' => 'id',
                    'solution_title' => 'title',
                    'submitted_at' => 'submittedAt',
                    'created_at' => 'createdAt',
                    'updated_at' => 'updatedAt',
                    'mark' => 'mark',
                    'reviewed_at' => 'reviewedAt',
                    'diagrams' => [
                        '_index' => 'diagram_id',
                        'diagram_id' => 'id',
                        'diagram_name' => 'name',
                        'image' => 'image',
                    ],
                    'reviewedBy' => [
                        '_index' => 'teacher_id',
                        '_single' => TRUE,
                        'teacher_id' => 'id',
                        'teacher_first_name' => 'firstName',
                        'teacher_last_name' => 'lastName'
                    ]
                ],
            ]
        ]);
    }

}