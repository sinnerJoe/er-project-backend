<?php

require_once(__DIR__."/../Model.php");

class User extends Model {
    public function __construct()
    {
        parent::__construct(__DIR__);
    }
    public function getAll() {
        return $this->fetchAll("getAll.sql");
    }
    
    public function getTeachers($year) {
        $data = $this->fetchAll("getTeachers.sql", ['year' => (int)$year]);
        return $this->orderData($data, [
            '_index'=> 'user_id',
            'user_id' => 'id',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'email' => 'email',
            'groups' => [
                '_index'=> 'group_id',
                'group_id' => 'id',
                'name' => 'name',
                'ed_year' => 'year'
            ]
        ]);
    }

    public function findUserByEmail($email) {
        return $this->fetchOne("findUserByEmail.sql", ["email" => $email]);
    }

    public function register($userData) {
        return $this->insert("register.sql", $userData)->lastInsertId();
    }

    public function registerSession($userId) {
        $data = array("sid" => session_id(), 'userId' => $userId);
        return $this->insert("registerLoginSession.sql", $data);
    }

    public function loginSession() {
        $data = array("sid" => session_id());
        return $this->fetchOne("loginSession.sql", $data);
    }

    public function deleteSession() {
        return $this->execute("deleteSession.sql", ["sid" => session_id()]);
    }

    public function deleteOtherSessions($userId) {
        return $this->execute("deleteOtherSessions.sql", [
            "sid" => session_id(), 
            "userId" => $userId
        ]);
    }

    public function changeGroup($userId, $groupId) {
        return $this->update('user_account', [
            'college_group_id' => ':groupId'
        ],[
           equality('user_id') 
        ], [
            'user_id' => $userId,
            'groupId' => $groupId
        ]);
    }

    public function fetchByRole($role) {
        $data = $this->fetchCustom('getAll.sql', [equality('role_level')], ['role_level' => $role]);

        return $this->orderData($data, [
            '_index' => 'user_id',
            'user_id' => 'id',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'email' => 'email',
            'created_at' => 'createdAt',
            'group' => [
                '_index' => 'college_group_id',
                '_single' => TRUE,
                'college_group_id' => 'id',
                'name' => 'name',
                'ed_year' => 'year'
            ]
        ]);
    }
}

?>