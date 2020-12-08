<?php

require_once(__DIR__."/../Model.php");

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

    public function getAssignment($id) {
        $data = $this->fetchCustom('getAssignment.sql',
            [equality('assign_id')],
            ['assign_id' => $id]
        );

        return $this->orderData($data, [
            'title' => 'title',
            'description' => 'description',
            'updated_at' => 'updatedAt'
        ])[0];
    }

    public function deleteAssignment($id) {
        return $this->delete('assign', [equality('assign_id')], ['assign_id' => $id]);
    }
}