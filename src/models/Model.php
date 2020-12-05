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

    private static function yes($el, $i, $arr) {
        return FALSE;
    }

    public static function accumulate_ordered($array, $prop, $initCb, $reducer, $stopCondition = ['Model', 'yes']) {
        $result = [];
        $lastResultElement = NULL;
        $lastVal = NULL;
        foreach($array as $i => $element) {
            if($stopCondition($element, $i, $array)) {
                break;
            }
            if($i == 0 || $lastVal != $element[$prop]) {
                $lastVal = $element[$prop];
                if($i !== 0) 
                array_push($result, $lastResultElement);
                $lastResultElement = $initCb($element, $i, $array);
            }
            $reducer($lastResultElement, $element, $i, $array);
        }

        array_push($result, $lastResultElement);

        return $result;
    }
}

?>