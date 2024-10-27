<?php
function login($user): true {

    session_regenerate_id(true);
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['weight'] = $user['weight_pounds'];
    $_SESSION['timezone'] = $user['timezone'];

    $seconds_in_month = 60 * 60 * 24 * 30;
    setcookie('user_id', $user['user_id'], time() + $seconds_in_month, null, null, true, true);

    return true;
}

function logout(): true {
    setcookie('user_id', '', time() - 3600);
    unset($_COOKIE['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['weight']);
    unset($_SESSION['timezone']);
    session_destroy();
    return true;
}

function is_logged_in(): bool {
    return isset($_COOKIE['user_id']);
}

function require_login(): void {
    if(!is_logged_in()) {
        redirect_to(url_for('index.php'));
    } else if (!isset($_SESSION['username'])) {
        session_regenerate_id(true);
        $user = find_user_by_id($_COOKIE['user_id']);
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['email'] = $user['weight_pounds'];
        $_SESSION['hashed_password'] = $user['password'];
    }
}
?>