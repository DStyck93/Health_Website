<?php
require_once('../../../private/initialize.php');
require_login();
$page_title = 'Profile';
global $errors;

if(is_post_request()) {
    $errors = update_user();
}

include(SHARED_PATH . '/header.php');
?>

<!-- Page Header -->
<h1>Your Profile</h1>
<?php include(SHARED_PATH . '/navigation.php');?>

<!-- Messages -->
<p id="message"><?php display_message() ?></p>
<p><?php echo display_errors($errors); ?></p>
<p>You must enter your password to confirm the updates.</p>

<form action="<?php echo url_for('/users/profile/index.php') ?>" method="POST">
    <dl><dt id="edit_profile">Username</dt>
        <dd><label><input type="text" placeholder="<?php echo h($_SESSION['username']); ?>" name="username"></label></dd>
    </dl>
    <dl><dt id="edit_profile">Email</dt>
        <dd><label><input type="text" placeholder="<?php echo h($_SESSION['email']); ?>" name="email"></label></dd>
    </dl>
    <dl><dt id="edit_profile">Password</dt>
        <dd><label><input type="password" placeholder="Enter your password" name="password"></label></dd>
    </dl>
    <dl><dt id="edit_profile">New Password</dt>
        <dd><label><input type="password" placeholder="New password" name="new_password"></label></dd>
    </dl>
    <dl><dt id="edit_profile">Confirm Password</dt>
        <dd><label><input type="password" placeholder="Confirm password" name="confirm_password"></label></dd>
    </dl>
    <input type="submit" value="Update Profile" id="profile_button">

</form><br>

<form action="<?php echo url_for('/users/profile/delete.php') ?>" method="POST">
    <input type="submit" value="Delete Profile" id="profile_button">
</form>


<?php include(SHARED_PATH . '/footer.php'); ?>