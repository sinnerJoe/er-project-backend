<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');
require_once(__DIR__.'/../../models/image/index.php');
require_once(__DIR__.'/../../models/solution/index.php');
require_once(__DIR__.'/../../models/account-confirmation/index.php');
require_once(__DIR__.'/../../models/group/index.php');

$router = new Router(false);

function generateConfirmationLink($id) {
    return Mail::$DOMAIN.'/confirm?id='.$id;
}

function sendConfirmationEmail($id, $email, $fullName) {
    return Mail::sendMail($email, 
        'Confirm your account on ER Platform',
        'Hello '.$fullName.', </br>'.
        'Please follow <a href="'.generateConfirmationLink($id).'">'.
        "this link </a> in order to activate the account registered on the <b>ER Platform</b>.".
        "</br>Please ignore this email if you didn't register on the website."
    );
}

$router->handlePost(function ($http, $body) {
    $user = new User();
    $confirmation = new AccountConfirmation();

    $email = $body['email'];
    $firstName = $body['first_name'];
    $lastName = $body['last_name'];
    
    if($user->findUserByEmail($email)) {
        $http->badRequest("A user with the same email is already registered.");
    }

    

    $data = array( 
        'password' => password_hash($body['password'], PASSWORD_BCRYPT),
        'email' => $email,
        'last_name' => $lastName,
        'first_name' => $firstName,
        'college_group_id' => $body['college_group']
    ); 


    $userId = $user->register($data);

    $confirmation->createFor($userId);
    $id = $confirmation->fetchRequestByUserId($userId)['account_confirmation_id'];

    sendConfirmationEmail($id, $email, $firstName.' '.$lastName);

    $http->ok(null, "You registerd successfully.");
});

$router->handleGet(function ($http, $body) {
    $user = new User();
    
    $sessionData = getSessionData();
    
    if($_GET['role'] === 'teacher') {
        is_teacher($http, $body);
        $http->ok($user->getTeachers($_GET['year'])); 
    }

    if($_GET['role'] === 'student') {
        is_teacher($http, $body);
        $http->ok($user->fetchByRole(10, $_GET['fromYear']));
    }

    if(isset($_GET['year'])) {
        is_admin($http, $body); 
        $http->ok($user->fetchByRegistrationYear($_GET['year']));
    }

    $http->ok($user->getShallowUserById($sessionData->userId));
})->addValidator(is_authenticated);

function checkCorrectPassword($userId, $password, $http) {
    $user = new User();
    $dbPassword = $user->getPassword($userId);
    if(!password_verify($password, $dbPassword)) {
        $http->badRequest('Wrong old password.');
    }
}

$router->handlePatch(function($http, $body) {
    $user = new User();

    $sessionData = getSessionData();
    $userId = $sessionData->userId;

    if($_GET['target'] === 'group') {
        is_teacher();
        $user->changeGroup($_GET['id'], $body['groupId']);
        $http->ok();
    }
    if($_GET['target'] === 'role') {
        if(!$sessionData->isAdmin) {
            $http->notAuthorized("You have to be an admin to change roles.");
        }
        $user->changeRole($_GET['id'], $body['role']);
        $http->ok();
    }

    if($_GET['target'] === 'password') {
        checkCorrectPassword($userId, $body['oldPassword'], $http);
        $user->changePassword($userId, password_hash($body['password'], PASSWORD_BCRYPT));
        $http->ok("Password successfully changed");
    }

    if($_GET['target'] === 'name') {
        $user->changeName($userId, $body['firstName'], $body['lastName']);
        $http->ok("Your name was successfully changed.");
    }
})->addValidator(is_authenticated);

$router->handleDelete(function($http, $body) {
    $sessionData = getSessionData();
    $deletedUserId = $sessionData->userId;
    if($_GET['id']) {
        if(!$sessionData->isAdmin) {
            $http->notAuthorized("You need to be an admin to delete user accounts");
        } 
        $deletedUserId = $_GET['id'];
    } else {
        checkCorrectPassword($deletedUserId, $body['password'], $http);
        logoutSession();
    }

    $image = new Image();
    $solution = new Solution();
    $user= new User();
    $group = new Group();

    $image->deleteImagesOfUser($deletedUserId);
    $solution->deleteSolutionsOfUser($deletedUserId);
    $solution->removeReviewer($deletedUserId);
    $group->removeCoordinator($deletedUserId);
    $user->deleteUser($deletedUserId);

    
    $http->ok("User successfully deleted.");

})->addValidator(is_authenticated);


?>