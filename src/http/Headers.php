<?php

    class Headers {
        static public function sendHeaders($actions=array('GET')) {
            // header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
                // you want to allow, and if so:
                    // header("Access-Control-Allow-Origin: $_SERVER['HTTP_ORIGIN']");
                    header('Access-Control-Allow-Credentials: true');
                    header('Access-Control-Allow-Origin: *');
                header('Access-Control-Max-Age: 1000');
                header('Content-Type: application/json;charset=utf-8');
                // header('Access-Control-Allow-Methods: '.implode(', ', $actions));
            } 
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                header('Access-Control-Allow-Origin: *');
                // header('Content-Type: application/json');
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
                   if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                    // header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Credentials, X-Requested-With');
                    header("Access-Control-Allow-Headers: accept-language, accept-encoding, cookie, ". $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
                exit(0);
            }
        }
    }

?>