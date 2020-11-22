<?php


require_once(__DIR__."/../../models/user/index.php");
require_once(__DIR__."/../../http/HttpResponse.php");

$user = new User();
$http = new HttpResponse();


// if(!isset($_SESSION['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
//     $http->notAuthorized('You must authenticate before using this service.');
//     exit();
// // }

// echo json_encode($user->getAll());
function foo($callback) {
    echo $callback('key');
}

foo(function ($str) {
    return str.str;
});

?>