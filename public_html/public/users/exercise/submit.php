<?php
require_once('../../../../private/initialize.php');
require_login();
if (!is_post_request()) {redirect_to(url_for('/users/diet/add.php'));}
include(SHARED_PATH . '/header.php');

// POST Values
$id = $_POST['id'] ?? null;
$length = $_POST['length'] ?? null;
$MET = $_POST['MET'] ?? null;
$description = $_POST['description'] ?? null;

if ($id == null || $length == null || $MET == null || $description == null) {
    $errors[] = "Error adding activity, please try again.";
    redirect_to(url_for('/users/exercise/add.php'));
}

// INSERT Query
if (add_activity($id, $MET, $length)) {
    $_SESSION['message'] = 'Your activity "' . $description . '" was successfully added.';
    redirect_to(url_for('/users/exercise/index.php'));
} else {
    $error[] = "Error adding activity, please try again.";
}

include(SHARED_PATH . '/footer.php'); ?>