<?php
require_once "../../../private/initialize.php";
require_login();
if(is_post_request()) {
    $id = $_POST['id'] ?? '';
    $servings = $_POST['servings'] ?? '';

    $result = add_food($id, $servings);

    if ($result) {
        $_SESSION['message'] = "Food added successfully!";
    } else {
        $errors[] = "Something went wrong";
    }
}
redirect_to(url_for('/users/diet/index.php'));
?>