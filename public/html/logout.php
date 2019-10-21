<?php
session_start();

if (isset($_SESSION["user_name"])) {
    echo 'Logoutしました。';
} else {
    echo 'SessionがTimeoutしました。';
}
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
@session_destroy();

$no_login_url = "../../index.php";
header("Location: {$no_login_url}");
exit;