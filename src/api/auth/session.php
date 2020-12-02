<?PHP

require_once(__DIR__.'/../../models/user/index.php');

function getSessionData() {
    $data = new stdClass;
    $data->authenticated = $_SESSION['authenticated'];
    $data->userId = $_SESSION['user_id'];
    $data->email = $_SESSION['email'];
    $data->role = $_SESSION['role_level'];
    $data->isAdmin = $data->role === 0;
    $data->isTeacher = $data->role === 5;
    $data->isStudent = $data->role === 10;

    return $data;
}

function registerSession($userId) {
    $user = new User();
    $user->registerSession($userId);
}

function loginSession() {
    if(session_status() != PHP_SESSION_ACTIVE) {
        echo "NO SESSION";
        return FALSE;
    }
    $user = new User();
    $userData = $user->loginSession();

    if(!is_array($userData)) {
        echo "USER NOT FOUND".session_id();
        return FALSE;
    }

    $_SESSION['email'] = $userData['email'];
    $_SESSION['user_id'] = $userData['user_id'];
    $_SESSION['role_level'] = $userData['role_level'];
    $_SESSION['authenticated'] = TRUE;

    return TRUE;
}

function logoutSession() {

    $sessionData = getSessionData();
    if(session_status() !== PHP_SESSION_ACTIVE or !$sessionData->authenticated) {
        return;
    }
    $user = new User();

    unset($_SESSION['email'], $sessionData->email);
    unset($_SESSION['user_id'], $sessionData->userId);
    unset($_SESSION['authenticated'], $sessionData->authenticated);
    unset($_SESSION['role_level'], $sessionData->role);

    $user->deleteSession();
}

function deleteOtherSessions() {
    if(session_status() != PHP_SESSION_ACTIVE) {
        return;
    }
    $sessionData = getSessionData();
    if($sessionData->authenticated) {
        $user = new User();
        $user->deleteOtherSessions($sessionData->userId);
    }
}

?>
