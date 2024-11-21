<?php
require_once('../../../../private/initialize.php');
require_login();

if (!is_post_request()) {redirect_to(url_for('/users/diet/add.php'));}

$page_title = 'Exercise';
include(SHARED_PATH . '/header.php');

$id = $_POST['id'] ?? '';
$activity = get_activity_by_id($id);
?>

<h1>Exercise</h1>
<?php include(SHARED_PATH . '/navigation.php'); ?>

<!-- Back Link -->
<p id="button"><a href="<?php echo url_for('/users/exercise/add.php') ?>">&laquo; Back</a></p>

<!-- Form -->
<h3>Input activity length and click submit.</h3>
<form action="<?php echo url_for('users/exercise/submit.php')?>" method="POST">

    <!-- ID -->
    <input type="hidden" name="id" value="<?php echo h($id)?>"/>

    <!-- Description -->
    <dl><dt id="exercise_dt">Activity</dt><dd>
        <input type="text" name="description" value="<?php echo h($activity['activity_description']);?>" readonly/>
    </dd></dl>

    <!-- MET -->
    <dl><dt id="exercise_dt">METs</dt><dd>
        <input type="text" name="MET" value="<?php echo h($activity['MET']);?>" readonly/>
    </dd></dl>

    <!-- Minutes -->
    <dl><dt id="exercise_dt">Minutes</dt><dd><input type="number" name="length"/></dd></dl>

    <!-- Submit -->
    <input type="submit" value="Submit" id="button"/>
</form>

<?php include(SHARED_PATH . '/footer.php'); ?>