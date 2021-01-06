<?php

require_once(__DIR__.'/../api/auth/session.php');

function is_authenticated($http, $request_body, $strict=FALSE) {
   $data = getSessionData();
   if(!$data->authenticated || $data->stale || $strict) {
      loginSession();
   } else {
      return;
   }
   $data = getSessionData();
   if(!$data->authenticated) {
      $http->notAuthenticated();
   }
}

function is_authenticated_strict($http, $request_body) {
   is_authenticated($http, $request_body, TRUE);
}

function is_teacher($http, $request_body) {
   $data = getSessionData();
   if($data->isAdmin || $data->isTeacher){
      return TRUE;
   }
   $http->notAuthorized('You must have at least teacher level privileges to access the resource. ' );
}

function is_admin($http, $request_body) {
   $data = getSessionData();
   if($data->role !== 0) {
      $http->notAuthorized('You must be an admin to access the resource.');
   }
}

?>