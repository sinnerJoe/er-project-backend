<?php

    class Headers {
        static public function sendHeaders($actions=array('GET')) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
                header('Access-Control-Allow-Origin: *');
                // header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 1000');
                header('Content-Type: application/json;charset=utf-8');
                // header('Access-Control-Allow-Methods: '.implode(', ', $actions));
            } 
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                // header('Content-Type: application/json');
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                    // may also be using PUT, PATCH, HEAD etc
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
                   if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                    // header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, X-Requested-With');
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                exit(0);
            }
        }
    }

?>