<?php
require_once "../../../private/initialize.php";
require_login();

$id = $_GET['id'] ?? '';

$result = remove_food($id);

if ($result) {
    $_SESSION['message'] = "Food removed.";
} else {
    $errors[] = "Something went wrong";
}

redirect_to(url_for('/users/diet/index.php'));
?>