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
<?php 
echo display_errors($errors) . "</br>";
echo display_message();
?>

<!-- Time frame Selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?><br>

<!-- Caloric Expenditures -->
<?php if ($time_frame =='day' || $time_frame == '') { ?><h2>Daily</h2>
<?php } elseif ($time_frame == 'week') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>
<h3>Calories Burned: <?php echo h($calories_burned) ?></h3>

<!-- Disclaimer -->
<p>Calories burned from activities are what was burned on top of your BMR. This difference will be added to your totals on the <a href="<?php echo url_for('users/diet/index.php')?>">Home</a> page.</p>

<hr size=3 color="black">

<!-- Table -->
<h2 id="table_title">Your Activities</h2>

<!-- Add Button -->
<form action="<?php echo url_for('/users/exercise/add.php'); ?>">
    <input type="submit" value="Add Activity" id="button"/>
</form><br>

<?php
if (!empty($user_activities)) { ?>

    <table class="list">
        <tr>
            <th>Date</th>
            <th>Activity</th>
            <th id="table_number">Minutes</th>
            <th id="table_number">Calories Burned</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($user_activities as $activity) { ?>
            <tr>
                <td><?php echo h(($activity['date_added'])->format('M j')); ?></td>
                <td id="long_text"><?php echo h($activity['description']); ?></td>
                <td id="table_number"><?php echo h($activity['minutes'])?></td>
                <td id="table_number"><?php echo h($activity['calories'])?></td>
                <td>
                    <!-- TODO - Add edit button. Use hidden activity code to get METs. -->
                    <form action="<?php echo url_for('/users/exercise/remove.php')?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo h($activity['id'])?>"/>
                        <input type="submit" name="remove" value="Remove" id="table_button"/>
                    </form>
                </td>
            </tr>
        <?php 
        }
        ?>

    </table>

    <?php

} else {
    echo "<p>You have no activities recorded for this time frame.</p>";
}
?>

<?php include(SHARED_PATH . '/footer.php');?>