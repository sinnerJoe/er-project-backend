<?php


class HttpResponse {
    public function badRequest($message) {
        http_response_code(400);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "error_type" => "invalid_parameter",
            "message" => $message
        ));
        exit();
    }

    public function serverFault($message) {
        http_response_code(500);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "error_type" => "internal_server_error",
            "message" => $message
        ));
        exit();
    }

    public function badMethod($message) {
        http_response_code(405);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "status" => "invalid_method",
            "message" => $message
        ));
        exit();
    }

    public function maximumCallsExceeded() {
        http_response_code(402);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "status" => "maximul_calls_exceeded",
            "message" => "Maximum calls executed"
        ));
        exit();
    }

    public function notFound($message) {
        http_response_code(404);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "status" => "not_found",
            "message" => $message 
        ));
        exit();
    }

    public function notAuthorized($message) {
        http_response_code(401);
        echo json_encode(array(
            "date" => date("d/m/Y H:i:s"),
            "status" => "unauthorized",
            "message" => $message
        ));
        exit();
    }

    
    public function ok($data=null, $message=null) {
        http_response_code(200);
        $response = array(
            "date" => date("d/m/Y H:i:s"),
            "status" => "success",
            "message" => $message,
            "data" => $data
        );
        if(!$message) {
            unset($response["message"]);
        }
        echo json_encode($response);
        exit();
    }

    


}

?>