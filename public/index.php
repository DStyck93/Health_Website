<!-- TODO - is_logged_in() not working on initial page load, but works if sign in -> back button. -->

<?php
require_once('../private/initialize.php');

if (is_logged_in()) {
    redirect_to(url_for('/users/index.php'));
}

include(SHARED_PATH . '/header.php');
?>

<h1 id="landing_header">Diet & Exercise</h1>

<h2>Sign in to begin your health journey.</h2>
<h3><a href="<?php echo WWW_ROOT . '/login.php' ?>">Sign In</h3>
<h3><a href=<?php echo WWW_ROOT . '/new.php' ?>>Create Account</h3>

<?php include(SHARED_PATH . '/footer.php');?>