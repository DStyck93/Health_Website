<?php
require_once('../../private/initialize.php');
require_login();

global $errors;

$time_frame = $_GET['tf'] ?? '';

// Get user nutrition data
$food_set = find_food_by_user($time_frame);
$total_nutrition = calculate_nutrition($food_set);
$calories = calculate_calories($total_nutrition);

$page_title = 'Home';
include(SHARED_PATH . '/header.php');
?>

<h1>Welcome <?php echo h($_SESSION['username']) ?></h1>

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
echo "</br><p>" . display_message() . "</p>";
?>

<!-- Timeframe selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?>

<?php if ($time_frame == 'day' || $time_frame == '') { ?><h2>Daily</h2>
<?php } else if ($time_frame == 'week') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>

<h3>Calories Consumed: <?php echo $calories ?></h3>
<h3>Calories Burned: Coming Soon</h3> <!-- TODO - Add calories burned. -->
<h3>Net Calories: <?php echo $calories ?></h3> <!-- TODO - Subtract calories burned -->

<?php

include (SHARED_PATH . '/footer.php');
?>