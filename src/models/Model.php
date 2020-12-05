<?php
require_once(__DIR__."/../config/database.php");
require_once(__DIR__."/../utils/utils.php");

class Model {
    private $db;
    private $sqlDir;
    public function __construct($scriptPath) {
        $this->sqlDir = $scriptPath.'/sql';
        $this->db = Database::getInstance();
    }

    private function prependPath($filePath) {
        return $this->sqlDir.'/'.$filePath;
    }

    private function readQueryFile($filePath) {
        $fullPath =$this->prependPath($filePath); 
        $file = fopen($fullPath, "r") or die("Unable to open query \"$filePath\"!");
        return fread($file,filesize($fullPath));
    }

    public function execute($queryFile, $arguments=null) {
        $query = $this->readQueryFile($queryFile);
        return $this->db->execute($query, $arguments)->affectedRows;
    }

    public function insert($queryFile, $arguments=null) {
        $query = $this->readQueryFile($queryFile);
        return $this->db->execute($query, $arguments)->pdo;
    }

    public function fetchOne($queryFile, $arguments=null) {
        $query = $this->readQueryFile($queryFile);
        return $this->db->fetchOne($query, $arguments);
    }

    public function fetchAll($queryFile, $arguments=null) {
        $query = $this->readQueryFile($queryFile);
        return $this->db->fetchAll($query, $arguments);
    }

    public static function pick($dict, $keys) {
        return pick($dict, $keys);
    }

    public function fetchCustom($queryFile, $clauses, $arguments) {
        $query = $this->readQueryFile($queryFile);
        $queryBuilder = new QueryBuilder($query);
        $queryBuilder->setCustomFilter(...$clauses);

        return $this->db->fetchAll($queryBuilder->build(), $arguments);
    }

    public function fetchTest($queryFile, $clauses, $arguments) {
        $query = $this->readQueryFile($queryFile);
        $queryBuilder = new QueryBuilder($query);
        $queryBuilder->setCustomFilter(...$clauses);

        echo $queryBuilder->build();
        exit();
 }

}

?>