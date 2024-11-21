<?php
require_once('../../../../private/initialize.php');
require_login();

$result = remove_user();
if($result){
    $_SESSION['message'] = 'User successfully deleted';
} else {
    $_SESSION['message'] = 'Unable to delete user';
}

redirect_to(url_for('logout.php'));
?>