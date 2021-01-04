<?php
require_once(__DIR__.'/../Model.php');

class ResetPassword extends Model {
    public function __construct(){
        parent::__construct(__DIR__);
    }
    public function getById ($id) {
        return $this->fetchCustom('getEntry.sql',
        [equality("restore_request_id",':id')], 
        ['id' => $id])[0];
    }

    public function createRequest($userId) {
        return $this->create('restore_request', [
            'restore_request_id' => 'UUID()',
            'user_id' => ':id',
            'expires' => 'NOW() + INTERVAL 1 DAY',
        ],['id' => $userId])->lastInsertId();
    }

    public function deleteUserRequests($userId) {
        return $this->delete('restore_request', [
            equality('user_id', ':id')
        ], ['id' => $userId]);
    }

    public function getByUserId ($id) {
        return $this->fetchCustom('getEntry.sql',
        [equality("user_id",':id')], 
        ['id' => $id])[0];
    }
}