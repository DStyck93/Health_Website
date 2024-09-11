<?php
require_once('../private/initialize.php');

if (isset($_COOKIE['user_id'])) { // Returning user
    $user = find_user_by_id($_COOKIE['user_id']);
    login($user);
    redirect_to(url_for('/users/index.php'));

} elseif (is_logged_in()) { // User tries to go back to landing page
    redirect_to(url_for('/users/index.php'));
}

include(SHARED_PATH . '/header.php');
?>

<h1>Diet & Exercise</h1>

<h2>Sign in to begin your health journey.</h2>
<h3><a href="login.php">Sign In</h3>
<h3><a href="new.php">Create Account</h3>

<?php include(SHARED_PATH . '/footer.php');?>