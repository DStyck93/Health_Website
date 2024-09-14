<?php
require_once('../../../private/initialize.php');
require_login();
global $errors;

$page_title = 'Diet';
include(SHARED_PATH . '/user_header.php');
?>

<!-- Page Header -->
<h1>Add Custom Food</h1>
<?php include(SHARED_PATH . '/navigation.php');?>

<!-- Back Link -->
<p id="back_button"><a href="<?php echo url_for('/users/diet/index.php') ?>">&laquo; Back</a></p>

<!-- Messages -->
<?php echo display_errors($errors);?>
<p><?php display_message() ?></p>


<?php include(SHARED_PATH . '/footer.php'); ?>
