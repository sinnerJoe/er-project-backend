<?php

function pick($dict, $keys) {
    $result = [];
    foreach($keys as $key) {
        $result[$key] = $dict[$key];
    }
    return $result;
}

function pick_translate($dict, $translations) {
    $result = [];
    foreach($translations as $key => $translation) {
        $result[$translation] = $dict[$key];
    }
    return $result;
}

function verify_columns_exist($dict, $translations) {
    if($_ENV['PROJECT_ENV'] !== 'development') {
        return;
    }
    $missing = [];
    foreach($translations as $key => $translation) {
        if(!array_key_exists($key, $dict)) {
            array_push($missing, $key.'('.$translation.')');
        }
    }
    if(count($missing)) {
        throw new Exception("The following columns are missing from the select: ".implode(', ', $missing));
    }
} 

class DataLayer {
    public $index;
    public $columns;
    public $children;
    public $last_index;
    public $only_child;
    
    public function __construct($config) {
        $this->columns = [];
        $this->children = [];
        $this->only_child = FALSE;
        $this->parseConfig($config);
    }

    public function hasChildren() {
        return count($this->children);
    }

    public function resetLastIndex() {
        $this->last_index = NULL;
    }

    public function select($array) {
        $this->last_index = $array[$this->index];
        verify_columns_exist($array, $this->columns);
        return pick_translate($array, $this->columns);
    }

    public function parseConfig($config) {
        foreach($config as $key => $value) {
            if($key === '_index') {
                $this->index = $value;
            } else if($key === '_single') {
                $this->only_child = $value;
            } else if(is_array($value)) {
                $this->children[$key] = new DataLayer($value);
            } else  {
                $this->columns[$key] = $value;
            }
        }
    }
}

class DataHierarchy {

    private $root_layer;

    private $result;

    public function __construct($config) {
        // var_dump($config);
        $this->root_layer = new DataLayer($config);
    }

    public function orderData($array) {

        if(!$array || count($array) == 0) {
            return [];
        }
        $result = [];

        for($i=0; $i<count($array); $i+=1) {
            $result = $this->captureData($result, $array[$i], $this->root_layer);
        }

        return $result;
    }

    private function captureData(&$array_to_append, $row, $currentLayer, $newParent = FALSE) {

        $dest_array = &$array_to_append;

        // if(is_array($dest_array) && !count($dest_array)) {
        //     return
        // }

        $didCapture = FALSE;

        $usedIndex = $currentLayer->index;

        if($newParent) {
            $currentLayer->resetLastIndex();
        }

        if($usedIndex === NULL || $row[$usedIndex] !== NULL) {
            if($usedIndex === NULL || count($array_to_append) === 0 || $newParent 
            || $row[$usedIndex] !== $currentLayer->last_index) {
                    if(!$currentLayer->only_child) {
                        array_push($dest_array, $currentLayer->select($row));
                    } else {
                        $dest_array = $currentLayer->select($row);
                    }   
                    $didCapture = TRUE;
            }
            if($currentLayer->hasChildren()) {
                foreach($currentLayer->children as $destination => $child) {
                    if(!$currentLayer->only_child)
                        $lastParent = &$dest_array[count($dest_array)-1];
                    else 
                        $lastParent = &$dest_array;
                    $next_dest = $lastParent[$destination];
                    if(!isset($next_dest) && !$child->only_child) $next_dest = [];

                    $lastParent[$destination] = $this->captureData(
                        $next_dest,
                        $row,
                        $child,
                        $didCapture
                    );
                }
                
                
            }
        }
        return $dest_array;
        
    }
}

class OrOp {
    private $operands;
    public function __construct(...$operands) {
        $this->operands = $operands; 
    }

    public function build() {
        $strings = [];

        foreach($this->operands as $operand) {
            if(is_string($operand)) {
                array_push($strings, $operand);
            } else if(is_object($operand)) {
                array_push($strings, $operand->build());
            }
        }
        return '('.implode(' OR ', $strings).')';
    }
}

class AndOp {
    private $operands;
    public function __construct(...$operands) {
        $this->operands = $operands;
    }
    public function build() {
        if(count($this->operands) === 0) {
            return '';
        }
        $strings = [];

        foreach($this->operands as $operand) {
            if(is_string($operand)) {
                array_push($strings, $operand);
            }
            else if(is_object($operand)) {
                array_push($strings, $operand->build());
            }
        }
        return '('.implode(' AND ', $strings).')';
    }
}


function isNull($value) {
    return $value.' IS NULL';
}

function isNotNull($value) { 
    return $value.' IS NOT NULL';
}

function inequality ($left, $right = NULL) {

        if($right === NULL) {
            $right = ':'.$left;
        }

        if($right === 'NULL' || $right === 'null') {
            return isNotNull($left);
        }

        return $left.' != '.$right;
}

function equality ($left, $right = NULL) {

        if($right === NULL) {
            $right = ':'.$left;
        }

        if($right === 'NULL' || $right === 'null') {
            return isNull($left);
        }

        return $left.' = '.$right;
}

function equalityOrNull($left, $right = NULL) {
    if($right === NULL) {
        $right = ':'.$left;
    }

    $obj = new OrOp(equality($left, $right), isNull($right));

    return $obj;
    
}

class QueryBuilder {
    private $query;
    private $filterExpression;
    public function __construct($query) {
        $this->query = $query;
        $this->filterExpression = NULL;
    }

    public function setCustomFilter(...$clauses) {
        if(count($clauses) !== 0) {
            $this->filterExpression = new AndOp(...$clauses);
        }
    }

    public function build() {
        $resultingQuery = $this->query;
        
        if($this->filterExpression !== NULL) {
            $resultingQuery = 'WITH FIRST_QUERY_LAYER AS ('.$resultingQuery.') SELECT * FROM FIRST_QUERY_LAYER WHERE '.$this->filterExpression->build();
        }
        return $resultingQuery;
    }

}


?>