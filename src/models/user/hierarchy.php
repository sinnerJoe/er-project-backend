<?php
require_once(__DIR__.'/../Model.php');

function organizeUsers($data) {
    $hierarchy = new DataHierarchy([
            '_index' => 'user_id',
            'user_id' => 'id',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'email' => 'email',
            'role_level' => 'role',
            'created_at' => 'createdAt',
            'disabled' => 'disabled',
            'evaluatedSolutions' => [
                '_index' => 'solution_id',
                'mark' => 'mark',
                'reviewed_at' => 'reviewedAt',
                'assignment' => [
                    '_index' => 'assign_id',
                    '_single' => TRUE,
                    'assign_id' => 'id',
                    'title' => 'title'
                ]
            ],
            'group' => [
                '_index' => 'college_group_id',
                '_single' => TRUE,
                'college_group_id' => 'id',
                'name' => 'name',
                'ed_year' => 'year'
            ]
    ]);

    return $hierarchy->orderData($data);
}