<?php
require_once('../../../../private/initialize.php');
require_login();

$page_title = 'Exercise';
include(SHARED_PATH . '/header.php');

// Get calories burned
$time_frame = $_GET['tf'] ?? '';
$user_activities = get_user_activities($time_frame);
$calories_burned = get_total_calories_burned($user_activities);
?>

<h1>Exercise</h1>

<?php include(SHARED_PATH . '/navigation.php'); ?>

<!-- Messages -->
<p><?php 
echo display_errors($errors) . "</br>";
echo display_message();
?></p>

<!-- Time frame Selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?><br>

<!-- Caloric Expenditures -->
<?php if ($time_frame =='day' || $time_frame == '') { ?><h2>Daily</h2>
<?php } elseif ($time_frame == 'week') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>
<h3>Calories Burned: <?php echo h($calories_burned) ?></h3>
<p>Note: This does not account for you Basal Metabolic Rate (BMR)</p> <!-- TODO - Account for BMR and edit/remove note -->

<!-- Add Button -->
<form action="<?php echo url_for('/users/exercise/add.php'); ?>">
    <input type="submit" value="Add Activity" id="button"/>
</form>

<!-- Table -->


<?php include(SHARED_PATH . '/footer.php');?>
