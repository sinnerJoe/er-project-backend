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
    public $destination;
    
    public function __construct($index, $destination, $columns) {
        $this->index = $index;
        $this->columns = $columns;
        $this->destination = $destination;
    }

    public function select($array) {
        return pick_translate($array, $this->columns);
    }
}

class DataHierarchy {

    private $layers;

    private $result;

    public function __construct($index, $columns) {
        $this->layers = [new DataLayer($index, NULL, $columns)];
    }

    public function addLayer($index, $destination, $columns) {
        array_push($this->layers, new DataLayer($index, $destination, $columns));
        return $this;
    }

    public function orderData($array) {

        if(!$array || count($array) == 0) {
            return [];
        }
        $result = [];

        for($i=0; $i<count($array); $i+=1) {
            $result = $this->captureData($result, $array[$i]);
        }

        return $result;
    }

    private function captureData(&$array_to_append, $row, $level = 0, $newParent = FALSE) {

        $layer = $this->layers[$level];
        
        $dest_array = &$array_to_append;

        $didCapture = FALSE;

        $usedIndex = $layer->index;

        if($usedIndex === NULL || $row[$usedIndex] !== NULL) {
            if($usedIndex === NULL || count($array_to_append) === 0 || $newParent 
            || $row[$usedIndex] !== $array_to_append[count($array_to_append) - 1][$usedIndex]) {
                    array_push($dest_array, $layer->select($row));
                    $didCapture = TRUE;
            }
            if($level + 1 < count($this->layers)) {
                // echo $this->layers[$level+1]->destination;
                $lastParent = &$dest_array[count($dest_array)-1];
                $destination_prop = $this->layers[$level+1]->destination;
                $next_dest = $lastParent[$destination_prop];
                if(!is_array($next_dest)) $next_dest = [];
                
                
                $lastParent[$destination_prop] = $this->captureData(
                    $next_dest,
                    $row,
                    $level + 1,
                    $didCapture
                );
            }
        }
        return $dest_array;
        
    }
}


?>