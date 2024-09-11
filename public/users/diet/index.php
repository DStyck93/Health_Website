<?php
require_once('../../../private/initialize.php');
require_login();
global $errors;
$user_id = $_SESSION['user_id'];

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

$page_title = 'Diet';
include(SHARED_PATH . '/user_header.php');
?>

<h1>Diet</h1>

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<div id="row">
    <div id="column">
        <h2>Daily</h2>
        <h3>Calories: <?php echo h($daily_calories) ?></h3>
        <h4>Carbs: <?php echo h($daily_total_nutrition['carb']) ?></h4>
        <h4>Fats: <?php echo h($daily_total_nutrition['fat']) ?></h4>
        <h4>Protein: <?php echo h($daily_total_nutrition['protein']) ?></h4>
    </div>

    <div id="column">
        <h2>Weekly</h2>
        <h3>Calories: <?php echo h($weekly_calories) ?></h3>
        <h4>Carbs: <?php echo h($weekly_total_nutrition['carb']) ?></h4>
        <h4>Fats: <?php echo h($weekly_total_nutrition['fat']) ?></h4>
        <h4>Protein: <?php echo h($weekly_total_nutrition['protein']) ?></h4>
    </div>

    <div id="column">
        <h2>Monthly</h2>
        <h3>Calories: <?php echo h($monthly_calories) ?></h3>
        <h4>Carbs: <?php echo h($monthly_total_nutrition['carb']) ?></h4>
        <h4>Fats: <?php echo h($monthly_total_nutrition['fat']) ?></h4>
        <h4>Protein: <?php echo h($monthly_total_nutrition['protein']) ?></h4>
    </div>
</div>

<form action="<?php echo url_for('/users/diet/add.php');?>">
    <input type="submit" value="Add Food" id="add_button"/>
</form>

<form action="<?php echo url_for('/users/diet/show.php');?>">
    <input type="submit" value="Show Food" id="show_button"/>
</form>

<?php
mysqli_free_result($daily_nutrition_set);
mysqli_free_result($weekly_nutrition_set);
mysqli_free_result($monthly_nutrition_set);

include(SHARED_PATH . '/footer.php');
?>