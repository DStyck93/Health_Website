<?php
require_once('../../private/initialize.php');
require_login();

$user_id = $_COOKIE['user_id'];

// Get user nutrition data
$daily_nutrition_set = get_user_nutrition($user_id, 'day');
$daily_total_nutrition = calculate_nutrition($daily_nutrition_set);
$daily_calories = calculate_calories($daily_total_nutrition);

$weekly_nutrition_set = get_user_nutrition($user_id, 'week');
$weekly_total_nutrition = calculate_nutrition($weekly_nutrition_set);
$weekly_calories = calculate_calories($weekly_total_nutrition);

$monthly_nutrition_set = get_user_nutrition($user_id, 'month');
$monthly_total_nutrition = calculate_nutrition($monthly_nutrition_set);
$monthly_calories = calculate_calories($monthly_total_nutrition);

$page_title = 'Home';
include(SHARED_PATH . '/header.php');
?>

<h1>Welcome <?php echo h($_SESSION['username']) ?></h1>

<?php include(SHARED_PATH . '/navigation.php'); ?>

<div id="content">

    <div id="timeframe_nav">
        <h2><a href="index.php?timeframe=daily">Daily</a></h2>
    </div>


    <div id="row">
        <div id="column">
            <h2>Daily</h2>
            <h3>Calories Consumed: <?php echo $daily_calories ?></h3>
            <h3>Calories Burned:</h3>
            <h3>Net Calories: <?php echo $daily_calories ?></h3>
        </div>

        <div id="column">
            <h2>Weekly</h2>
            <h3>Calories Consumed: <?php echo $weekly_calories ?></h3>
            <h3>Calories Burned:</h3>
            <h3>Net Calories: <?php echo $weekly_calories ?></h3>
        </div>

        <div id="column">
            <h2>Monthly</h2>
            <h3>Calories Consumed: <?php echo $monthly_calories ?></h3>
            <h3>Calories Burned:</h3>
            <h3>Net Calories: <?php echo $monthly_calories ?></h3>
        </div>
    </div>
</div>



<?php
mysqli_free_result($daily_nutrition_set);
mysqli_free_result($weekly_nutrition_set);
mysqli_free_result($monthly_nutrition_set);

include (SHARED_PATH . '/footer.php');
?>