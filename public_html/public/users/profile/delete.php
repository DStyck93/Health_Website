<?php
require_once('../../../../private/initialize.php');
require_login();

$result = remove_user();
if($result){
    $_SESSION['message'] = 'User successfully deleted';
} else {
    $_SESSION['message'] = 'Unable to delete user';
}

setcookie('user_id', '', time() - 3600);
unset($_COOKIE['user_id']);

logout();

redirect_to(url_for('/index.php'));
?>