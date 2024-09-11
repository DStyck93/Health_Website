<?php
require_once "../../../private/initialize.php";
require_login();

$id = $_GET['id'] ?? '';

$result = add_food($id);

if ($result) {
    $_SESSION['message'] = "Food added successfully!";
} else {
    $errors[] = "Something went wrong";
}

redirect_to(url_for('/users/diet/index.php'));
?>