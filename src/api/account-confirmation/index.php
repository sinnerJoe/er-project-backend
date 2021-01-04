<?PHP

require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/account-confirmation/index.php');
require_once(__DIR__.'/../../http/Router.php');

$router = new Router();


$router->handlePost(function($http, $body) {
   $confirmation = new AccountConfirmation();
   $user = new User();
   $id = $_GET['id'];
   $request = $confirmation->fetchRequest($id);

   if(!$request) {
       $http->notFound();
   }

   $confirmation->deleteRequest($id);
   $userId = $request['user_id'];

   $user->activateAccount($userId);

   $http->ok("Account activated");

});