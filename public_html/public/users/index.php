<?php
require_once('../../../private/initialize.php');
require_login();

global $errors;

$time_frame = $_GET['tf'] ?? '';

// Food Info
$food_set = find_food_by_user($time_frame);
$total_nutrition = calculate_nutrition($food_set);
$calories = calculate_calories($total_nutrition);

// Activity Info
$user_activities = get_user_activities($time_frame);
$calories_burned = get_total_calories_burned($user_activities);

$page_title = 'Home';
include(SHARED_PATH . '/header.php');
?>

<h1>Welcome <?php echo h($_SESSION['username']) ?></h1>

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
echo "</br><p>" . display_message() . "</p>";
?>

<!-- Time frame selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?><br>

<?php if ($time_frame == 'day' || $time_frame == '') { ?><h2>Daily</h2>
<?php } else if ($time_frame == 'week') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>

<!-- BMR Calculations -->
<?php
$kg = convert_lbs_to_kg($_SESSION['weight']);
$minutes_in_day = 60 * 24;
$daily_bmr = calculate_calories_burned(1.0, $kg, $minutes_in_day);
if ($time_frame == 'day' || $time_frame == '') $bmr = $daily_bmr;
elseif ($time_frame == 'week') $bmr = $daily_bmr * 7;
else $bmr = $daily_bmr * 30; 
?>

<!-- Health Summary -->
<h3>Calories Consumed: <?php echo number_format($calories, 0) ?></h3>
<h3>Activity Calories Burned: <?php echo $calories_burned?></h3>
<h3>Basal Metabolic Rate (BMR): <?php echo number_format($bmr, 0) ?></h3>
<h3>Net Calories: <?php echo number_format($calories - $calories_burned - $bmr, 0) ?></h3>

<hr size=3 color="black">

<!-- How To -->
<h2>How to Use MyHealthApp</h2>
<p><ul>
    <li>Here on the home screen you can see an overview of the net calories you have recorded.</li>
    <li>You can filter this display to show results from today, the last 7 days, or the last 30 days.</li>
    <li>The <a href="<?php echo url_for('users/diet/index.php')?>">Diet</a> page allows you to add food and track your calories consumed. You can use food from the database, or add your own.</li>
    <li>The <a href="<?php echo url_for('users/exercise/index.php')?>">Exercise</a> page allows you to track your activities.</li>
    <li>To estimate the amount of calories burned from you activities, you will need to add your weight to your <a href="<?php echo url_for('users/profile/index.php')?>">profile</a>.</li>
    <li>BMR is the rate at which you burn calories while inactive</li>
</ul></p>

<hr size=3 color="black">

<!-- Disclaimers -->
<h2>Disclaimers</h2>
<p><ul>
    <li>All calculations are an estimate. It is impossible to gauge numbers accurately without the proper equipment.</li>
    <li>Consuming too few or too many calories can negatively affect your health. Talk to your doctor to ensure a healthy diet plan.</li>
    <li>Food data was retrieved from <a href="https://www.myfooddata.com/">MyFoodData</a>. They use data provided by the <a href="https://fdc.nal.usda.gov/">United States Department of Agriculture</a>, but have made it more user friendly</li>
    <li>Activity data was retrieved from the <a href="https://pacompendium.com/">2024 Compendium of Physical Activities</a>. Some activity descriptions and categorizations have been adjusted.</a></li>
    <li>Caloric expenditure estimations are based on <a href="https://journals.lww.com/acsm-healthfitness/fulltext/2023/03000/metabolic_calculations_cases.4.aspx">Metabolic Calculations Cases</a> from the ACSM's Health & Fitness Journal</li>
</ul></p>

<hr size=3 color="black">

<!-- Patch Notes -->
<h2>Patch Notes</h2>
<p>The following changes have been made for version 2.0 (November 2, 2024):
    <ul>
        <li>Users can now track their workout activities on the <a href="<?php echo url_for('users/exercise/index.php')?>">Exercise</a> page</li>
        <li>User wight has been added to the <a href="<?php echo url_for('users/profile/index.php')?>">Profile</a> page and is required to estimate the number of calories you have burned.</li>
    </ul>
</p>

<hr size=3 color="black">

<?php
include(SHARED_PATH . '/footer.php');
?>