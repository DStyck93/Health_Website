<?php
require_once('../../../../private/initialize.php');
require_login();

$page_title = 'Exercise';
include(SHARED_PATH . '/header.php');
?>

<h1>Exercise</h1>

<?php include(SHARED_PATH . '/navigation.php'); ?>

<!-- Time frame Selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?><br>

<!-- Add Button -->
<form action="<?php echo url_for('/users/exercise/add.php'); ?>">
    <input type="submit" value="Add Activity" id="button"/>
</form>

<?php include(SHARED_PATH . '/footer.php'); ?>
