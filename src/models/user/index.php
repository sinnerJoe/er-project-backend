<?php

require_once(__DIR__."/../Model.php");
require_once(__DIR__."/hierarchy.php");

class User extends Model {
    public function __construct()
    {
        parent::__construct(__DIR__);
    }

    public function getAll($conditions=[], $args=[]) {
        $args['year'] = $args['year'];
        
        return $this->fetchCustom('getAll.sql', $conditions, $args);
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
        $data = $this->getAll([equality('role_level')], ['role_level' => $role]);

        return organizeUsers($data);
    }

    public function fetchByRegistrationYear($year) {
        $data = $this->getAll([], ['year' => $year]);

        return organizeUsers($data);
    }

    public function changeRole($userId, $role) {
        return $this->update('user_account', [
            'role_level' => ':role'
        ], [
            equality('user_id')
        ], [
            'user_id' => $userId,
            'role' => $role
        ]);
    }

    public function deleteUser($userId) {
        $cond = [equality('user_id')];
        $args =  ['user_id' => $userId];
        $this->delete('user_session', $cond, $args);
        $this->delete('user_account', $cond, $args);
    }

    public function getPassword($userId) {
        $data = $this->fetchOne('getPassword.sql', ['user_id' => $userId]);
        if(!$data) {
            return '';
        }
        return $data['password'];
    }

    public function changePassword($userId, $password) {
        return $this->update('user_account', 
            ['password' => ':password'], 
            [equality('user_id')],
            [
                'user_id' => $userId,
                'password' => $password
            ]
        );
    }

    public function changeName($userId, $firstName, $lastName) {
        return $this->update('user_account',
            [
                'first_name' => ':firstName',
                'last_name' => ':lastName'
            ],
            [equality('user_id')],
            [
                'user_id' => $userId,
                'lastName' => $lastName,
                'firstName' => $firstName
            ]
        );
    }

    public function getShallowUserById($userId) {
        $data = $this->fetchCustom('getShallowUser.sql', [equality(user_id)], ['user_id' => $userId]);
        return $this->orderData($data, [
            '_index' => 'user_id',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'email' => 'email'
        ])[0];
    }
}

?>