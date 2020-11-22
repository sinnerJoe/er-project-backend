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
        return $this->fetchOne("findUserByEmail.sql",["email" => $email]);
    }

    public function register($userData) {
        return $this->insert("register.sql", $userData)->lastInsertId();
    }
}

?>