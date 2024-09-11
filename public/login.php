<?php
require_once('../private/initialize.php');
global $errors;
$user = ['username' => '', 'password' => ''];

if (is_post_request()) {
    // Get form values
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = find_user_by_username($username);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            login($user);
            redirect_to(url_for('/users/index.php'));
        } else {
            $errors[] = "Invalid username or password";
        }
    } else {
        $errors[] = "Invalid username or password";
    }
}

include(SHARED_PATH . '/header.php');
?>

<h1>Sign In</h1>

<p><?php echo display_errors($errors) ?></p>

<a href="index.php">&laquo; Back</a>

<form method="post">
    <!-- Username -->
    <dl><dt>Username</dt>
        <dd><label><input type="text" name="username"></label></dd>
    </dl>
    <!-- Password -->
    <dl><dt>Password</dt>
        <dd><label><input type="password" name="password"</label></dd>
    </dl>
    <!-- Submit Button -->
    <label><input type="submit" value="Sign In"></label>
</form>

<?php include(SHARED_PATH . '/footer.php');?>