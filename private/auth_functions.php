<?php

// TODO - Remove login cookie and replace with Session Token
function login($user): true {
    session_regenerate_id();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['last_login'] = time();
    $_SESSION['username'] = $user['username'];

    $login_time = time() + 60 * 60 * 24 * 7 * 4; // Time till login expires
    setcookie("user_id", $user['user_id'], $login_time);
    setcookie("last_login", time(), $login_time);

    return true;
}

function logout(): true {
    unset($_SESSION['user_id']);
    unset($_SESSION['last_login']);
    unset($_SESSION['username']);

    setcookie("user_id", "", time() - 3600); // Remove cookie

    return true;
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if(!is_logged_in()) {
        redirect_to(url_for('index.php'));
    }
}

?>