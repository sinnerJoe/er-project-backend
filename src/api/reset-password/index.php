<?PHP

require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/reset-password/index.php');
require_once(__DIR__.'/../../http/Router.php');

$router = new Router();

function generateResetLink($id) {
    return Mail::$DOMAIN.'/reset-password?id='.$id;
}

$router->handlePost(function($http, $body) {
   $resetPass = new ResetPassword();
   $user = new User();
   $email = $body['email'];
   $data = $user->findUserByEmail($email);

   if(!$data) {
       $http->notFound('User with the email address '.$email.' doesn\'t exist');
   }

   if((int)$data['disabled']) {
       $http->badRequest('The account wasn\'t enabled yet');
   }

   $resetPass->deleteUserRequests($data['user_id']);
   $resetPass->createRequest($data['user_id']);

   $requestId = $resetPass->getByUserId($data['user_id'])['restore_request_id'];
   
   $sent = Mail::sendMail($email, 
    'Reset password', 
    'Hello, please follow <a href="'
    .generateResetLink($requestId)
    .'">this link</a> to reset your password. If this email wasn\'t sent by you, ignore it.');

    if($sent) {
        $http->ok(NULL, "Email sent successfully.");
    } else {
        $http->serverFault("Could't send the message to the email address");
        $resetPass->deleteUserRequests($data['user_id']);
    }
});

$router->handleGet(function($http, $body) {
   $resetPass = new ResetPassword();
   $data = $resetPass->getById($_GET['id']);
   if($data) {
       $http->ok(null);
   } else {
       $http->notFound("The reset token expired.");
   }
});

$router->handlePut(function($http, $body) {
    $resetPass = new ResetPassword();
    $user = new User();
    $password = $body['password'];
    $id = $_GET['id'];

    $data = $resetPass->getById($id);
    if(!$data) {
        $http->notFound('The reset token expired.');
    }
    $user->changePassword($data['user_id'], password_hash($password, PASSWORD_BCRYPT));

    $resetPass->deleteUserRequests($data['user_id']);

    $http->ok(null, "Password changed successfully. Try to authenticate.");
    
});