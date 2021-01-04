<?php
require_once(__DIR__.'/../Model.php');

class AccountConfirmation extends Model {
    public function __construct(){
        parent::__construct(__DIR__);
    }

    public function createFor($userId) {
        return $this->create('account_confirmation', [
            'account_confirmation_id' => 'UUID()',
            'user_id' => ':id',
        ],['id' => $userId])->lastInsertId();
    }

    public function fetchRequest($id) {
        return $this->fetchCustom('getEntry.sql', 
            [equality('account_confirmation_id', ':id')], 
            ['id' => $id]
        )[0];
    }

    public function fetchRequestByUserId($id) {
        return $this->fetchCustom('getEntry.sql', 
            [equality('user_id', ':id')], 
            ['id' => $id]
        )[0];
    }

    public function deleteRequest($id) {
        return $this->delete('account_confirmation', [equality('account_confirmation_id', ':id')], ['id' => $id]);
    }
}