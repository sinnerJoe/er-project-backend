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

    public function fetchAll($queryFile, $arguments=[]) {
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

    public function update($table, $assignments, $clauses, $arguments) {
        $query = 'UPDATE '.$table. ' SET ';
        $equalities = [];
        foreach($assignments as $column => $value) {
            array_push($equalities, $column.' = '.$value);
        }
        $query = $query.implode(', ', $equalities);
        if(count($clauses) == 0) {
            $where = '';
        } else {
            $expression = new AndOp(...$clauses);
            $where = 'WHERE '.$expression->build();
        }
        $query = $query.' '.$where;
        echo $query;
        return $this->db->execute($query, $arguments);
    }

    public function create($table, $columns, $arguments) {
        $column_names = array_keys($columns);
        $column_values = [];
        foreach($column_names as $name) {
            array_push($column_values, $columns[$name]);
        }

        $columns_str = implode(', ', $column_names);
        $values_str = implode(', ', $column_values);

        $query = "INSERT INTO ".$table."(".$columns_str.") VALUES (".$values_str.")";

        return $this->db->execute($query, $arguments);
    }

    public function delete($table, $clauses, $arguments) {
        $expression = new AndOp(...$clauses);

        $query = "DELETE FROM ".$table." WHERE".$expression->build();

        return $this->db->execute($query, $arguments);
    }

    public function orderData($data, $config) {
        $obj = new DataHierarchy($config);

        return $obj->orderData($data);
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