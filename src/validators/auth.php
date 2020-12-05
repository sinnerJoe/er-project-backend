<?php

require_once(__DIR__.'/../api/auth/session.php');

function is_authenticated($http, $request_body) {
   $data = getSessionData();
   if(!$data->authenticated) {
      loginSession();
   } else {
      return;
   }
   $data = getSessionData();
   if(!$data->authenticated) {
      $http->notAuthenticated();
   }
}

function is_teacher($http, $request_body) {
   $data = getSessionData();
   if($data->role === 5 || $data->role === 0){
      return TRUE;
   }
   $http->notAuthorized('You must have at least teacher level privileges to access the resource.');
}

function is_admin($http, $request_body) {
   $data = getSessionData();
   if($data->role !== 0) {
      $http->notAuthorized('You must be an admin to access the resource.');
   }
}

?>