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
}

?>