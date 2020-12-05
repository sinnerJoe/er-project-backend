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

class DataLayer {
    public $index;
    public $columns;
    public $children;
    public $last_index;
    
    public function __construct($config) {
        $this->columns = [];
        $this->children = [];
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
        return pick_translate($array, $this->columns);
    }

    public function parseConfig($config) {
        foreach($config as $key => $value) {
            if($key === '_index') {
                $this->index = $value;
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

        $didCapture = FALSE;

        $usedIndex = $currentLayer->index;

        if($newParent) {
            $currentLayer->resetLastIndex();
        }

        if($usedIndex === NULL || $row[$usedIndex] !== NULL) {
            if($usedIndex === NULL || count($array_to_append) === 0 || $newParent 
            || $row[$usedIndex] !== $currentLayer->last_index) {
                    array_push($dest_array, $currentLayer->select($row));
                    $didCapture = TRUE;
            }
            if($currentLayer->hasChildren()) {
                foreach($currentLayer->children as $destination => $child) {
                    $lastParent = &$dest_array[count($dest_array)-1];
                    $next_dest = $lastParent[$destination];
                    if(!is_array($next_dest)) $next_dest = [];

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


?>