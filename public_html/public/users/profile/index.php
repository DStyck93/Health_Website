<?php
require_once('../../../../private/initialize.php');
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

<!-- Form -->
<form action="<?php echo url_for('/users/profile/index.php') ?>" method="POST">
    <!-- Username -->    
    <dl><dt id="edit_profile">Username</dt><dd>
        <input type="text" id="form_input" placeholder="<?php echo h($_SESSION['username']); ?>" name="username"/>
    </dd></dl>
    <!-- Email -->
    <dl><dt id="edit_profile">Email</dt><dd>
        <input type="text" id="form_input" placeholder="<?php echo h($_SESSION['email']); ?>" name="email"/>
    </dd></dl>
    <!-- Weight -->
    <dl><dt id="edit_profile">Weight (lbs)</dt><dd>
        <input type="number" id="form_input" placeholder="<?php echo h($_SESSION['weight']);?>" name="weight"/>
    </dd></dl>
    <!-- Timezone -->
    <dl><dt id="edit_profile">Timezone (USA)</dt><dd>
        <select name="timezone">
            <option value="America/New_York" <?php if(h($_SESSION['timezone']) == 'America/New_York') echo "selected=\"selected\" ";?>>Eastern</option>
            <option value="America/Chicago" <?php if(h($_SESSION['timezone']) == 'America/Chicago') echo "selected=\"selected\" ";?>>Central</option>
            <option value="America/Denver" <?php if(h($_SESSION['timezone']) == 'America/Denver') echo "selected=\"selected\" ";?>>Mountain</option>
            <option value="America/Los_Angeles" <?php if(h($_SESSION['timezone']) == 'America/Los_Angeles') echo "selected=\"selected\" ";?>>Pacific</option>
            <option value="America/Phoenix" <?php if(h($_SESSION['timezone']) == 'America/Phoenix') echo "selected=\"selected\" ";?>>Phoenix</option>
            <option value="America/Anchorage" <?php if(h($_SESSION['timezone']) == 'America/Anchorage') echo "selected=\"selected\" ";?>>Alaska</option>
            <option value="Pacific/Honolulu" <?php if(h($_SESSION['timezone']) == 'Pacific/Honolulu') echo "selected=\"selected\" ";?>>Hawaii</option>
        </select>
    </dd></dl>
    <!-- Password -->
    <dl><dt id="edit_profile">Password</dt><dd>
        <input type="password" id="form_input" placeholder="Enter your password" name="password"/>
    </dd></dl>
    <dl><dt id="edit_profile">New Password</dt><dd>
        <input type="password" id="form_input" placeholder="New password" name="new_password"/>
    </dd></dl>
    <dl><dt id="edit_profile">Confirm Password</dt><dd>
        <input type="password" id="form_input" placeholder="Confirm password" name="confirm_password"/>
    </dd></dl>
    <!-- Submit -->
    <input type="submit" value="Update Profile" id="edit_user_button"/>

</form><br>

<!-- Delete -->
<form action="<?php echo url_for('/users/profile/delete.php') ?>" method="POST">
    <input type="submit" value="Delete Profile" id="edit_user_button"/>
</form>

<?php include(SHARED_PATH . '/footer.php'); ?>