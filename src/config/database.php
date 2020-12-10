<?php

require_once(__DIR__.'/config.php');

class Database {
    static private $instance;
    private $hostName;
    private $dbname;
    private $username = "er_admin";
    private $password = "root";
    private $pdo;

    public function __construct() {
        $this->pdo = null;
        $this->hostName = $_ENV["PMA_HOST"];
        $this->dbname = $_ENV["DATABASE_NAME"];
        try {
            $this->pdo = new PDO("mysql:host=$this->hostName;dbname=$this->dbname;port=3306",
            $this->username, $this->password); 
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Error : ".$e->getMessage();
            die();
        }
    }

    public function fetchAll($query, $arguments){
        $stmt = $this->pdo->prepare($query);
        if($arguments) $stmt->execute($arguments);
        else $stmt->execute();
        $rowCount = $stmt->rowCount();
        if($rowCount <= 0) {
            return [];
        }

        return $stmt->fetchAll();

    }

    public function fetchOne($query, $arguments) {
        $stmt = $this->pdo->prepare($query);

        if($arguments) $stmt->execute($arguments);

        $rowCount = $stmt->rowCount();
        if($rowCount <= 0) {
            return null;
        }

        return $stmt->fetch();
    }

    static public function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
        
    }

    public function execute($query, $arguments=array()) {
        $stmt = $this->pdo->prepare($query);
        $result = new stdClass;
        if($arguments) {
            $result->affectedRows = $stmt->execute($arguments);
        }
        else {
            $result->affectedRows = $stmt->execute();
        }
        $result->pdo = $this->pdo;
        return $result;
    }
}

?>