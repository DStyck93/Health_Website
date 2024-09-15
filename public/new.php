<?php
require_once('../private/initialize.php');
global $errors;
$user = [];

// If Create Account Button is clicked
if(is_post_request()) {

    // Get data from form
    $user['username'] = $_POST['username'] ?? '';
    $user['email'] = $_POST['email'] ?? '';
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
    $user["password"] = '';
}

$page_title = 'Sign Up';
include(SHARED_PATH . '/header.php');
?>

<h1>Create Account</h1>

<p><?php echo display_errors($errors); ?></p>

<a href="<?php echo url_for('index.php') ?>">&laquo; Back</a>

<form action="" method="post">
    <!-- Username -->
    <dl><dt>Username</dt>
        <dd><label><input type="text" name="username" value="<?php echo h($user['username'])?>"></label></dd>
    </dl>
    <!-- Email -->
    <dl><dt>Email</dt>
        <dd><label><input type="email" name="email" value="<?php echo h($user['email'])?>"></label></dd>
    </dl>
    <!-- Password -->
    <dl><dt>Password*</dt>
        <dd><label><input type="password" name="password"</label></dd>
    </dl>
    <dl><dt>Confirm Password</dt><dd><label><input type="password" name="password_confirm"</label></dd></dl><br>
    <!-- Submit -->
    <label><input type="submit" value="Create Account"</label>
</form>

<p>*Password must be at least 5 characters long and contain 1 uppercase, lowercase, and special character.</p>

<?php include(SHARED_PATH . '/footer.php');?>