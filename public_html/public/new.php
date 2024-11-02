<?php
require_once('../../private/initialize.php');
global $errors;
$user = [];

// If Create Account Button is clicked
if(is_post_request()) {

    // Get data from form
    $user['username'] = $_POST['username'] ?? '';
    $user['email'] = $_POST['email'] ?? '';
    $user['weight'] = $_POST['weight'] ?? '';
    $user['timezone'] = $_POST['timezone'] ?? '';
    $user['password'] = $_POST['password'] ?? '';
    $user['password_confirm'] = $_POST['password_confirm'] ?? '';

    // Login
    $result = add_user($user);
    if ($result === true) {
        $user = find_user_by_username($user['username']);
        login($user);
        redirect_to(url_for('/users/index.php'));
    } else {
        $errors = $result;
    }

} else { // display the blank form
    $user["username"] = '';
    $user["email"] = '';
    $user["weight"] = '';
    $user["timezone"] = 'America/Chicago';
    $user["password"] = '';
}

$page_title = 'Sign Up';
include(SHARED_PATH . '/header.php');
?>

<h1 id="landing_header">Create Account</h1>

<p><?php echo display_errors($errors); ?></p>

<a id="button" href="<?php echo url_for('index.php') ?>">&laquo; Back</a><br>

<p>*Password must be at least 5 characters long and contain at least 1 uppercase, lowercase, number, and special character.</p>

<form action="" method="post">
    <!-- Username -->
    <dl><dt id="new_account">Username</dt><dd>
        <input type="text" name="username" value="<?php echo h($user['username']);?>"/>
    </dd></dl>
    <!-- Email -->
    <dl><dt id="new_account">Email</dt><dd>
        <input type="email" name="email" value="<?php echo h($user['email']);?>"/>
    </dd></dl>
    <!-- Weight -->
    <dl><dt id="new_account">Weight (lbs)</dt><dd>
        <input type="number" name="weight" value="<?php echo h($user['weight']);?>"/>
    </dd></dl>
    <!-- Timezone -->
    <dl><dt id="new_account">Timezone (USA)</dt><dd>
        <select name="timezone">
            <option value="America/New_York" <?php if(h($user['timezone']) == 'America/New_York') echo "selected=\"selected\" ";?>>Eastern</option>
            <option value="America/Chicago" <?php if(h($user['timezone']) == 'America/Chicago') echo "selected=\"selected\" ";?>>Central</option>
            <option value="America/Denver" <?php if(h($user['timezone']) == 'America/Denver') echo "selected=\"selected\" ";?>>Mountain</option>
            <option value="America/Los_Angeles" <?php if(h($user['timezone']) == 'America/Los_Angeles') echo "selected=\"selected\" ";?>>Pacific</option>
            <option value="America/Phoenix" <?php if(h($user['timezone']) == 'America/Phoenix') echo "selected=\"selected\" ";?>>Phoenix</option>
            <option value="America/Anchorage" <?php if(h($user['timezone']) == 'America/Anchorage') echo "selected=\"selected\" ";?>>Alaska</option>
            <option value="Pacific/Honolulu" <?php if(h($user['timezone']) == 'Pacific/Honolulu') echo "selected=\"selected\" ";?>>Hawaii</option>
        </select>
    </dd></dl>
    <!-- Password -->
    <dl><dt id="new_account">Password*</dt><dd><input type="password" name="password"></dd></dl>
    <dl><dt id="new_account">Confirm Password</dt><dd><input type="password" name="password_confirm"></dd></dl><br>
    <!-- Submit -->
    <input type="submit" value="Create Account" id="button">
</form>

<?php include(SHARED_PATH . '/footer.php');?>