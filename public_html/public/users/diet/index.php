<!-- TODO - Limit to 50 results per page & add page selection -->

<?php
require_once('../../../../private/initialize.php');
require_login();

global $errors;

$time_frame = $_GET['tf'] ?? '';

// Get user nutrition data
$food_set = find_food_by_user($time_frame);
$total_nutrition = calculate_nutrition($food_set);
$calories = calculate_calories($total_nutrition);

// Remove food
if(is_post_request()) {
    try {
        $result = remove_food($_SESSION['remove_id']);
        $_SESSION['message'] = "Food removed!";
    } catch (exception) {
        $errors[] = "Couldn't remove food!";
    }
    unset($_SESSION['remove_id']);
}

$page_title = 'Diet';
include(SHARED_PATH . '/header.php');
?>

<h1>Diet</h1>

<?php include(SHARED_PATH . '/navigation.php'); ?>

<?php echo display_errors($errors);
echo "</br><p>" . display_message() . "</p>";
?>

<!-- Timeframe Selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?>

<!-- Nutrition Numbers -->

<?php if($time_frame =='day' || $time_frame == '') { ?><h2>Daily</h2>
<?php } else if($time_frame == 'week') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>

<h3>Calories: <?php echo h($calories) ?></h3>
<h4>Carbs: <?php echo h($total_nutrition['carb']) ?></h4>
<h4>Fats: <?php echo h($total_nutrition['fat']) ?></h4>
<h4>Protein: <?php echo h($total_nutrition['protein']) ?></h4>

<!-- Add Food Button -->
<form action="<?php echo url_for('/users/diet/add.php'); ?>">
    <input type="submit" value="Add Food" id="button"/>
</form>

<!-- User Food Table -->
<h2 id="table_title">Your Food</h2>
<?php
if (!empty($food_set)) { ?>

    <table class="list">
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th id="table_number">Calories</th>
            <th id="table_number">Carbs</th>
            <th id="table_number">Fats</th>
            <th id="table_number">Protein</th>
            <th id="table_number">Servings</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($food_set as $food) { ?>
            <tr>
                <td><?php echo h(($food['date_added'])->format('M j')); ?></td>
                <td id="long_text"><?php echo h($food['food_name']); ?></td>
                <td id="table_number"><?php echo calculate_calories($food)?></td>
                <td id="table_number"><?php echo h($food['carb']); ?></td>
                <td id="table_number"><?php echo h($food['fat']); ?></td>
                <td id="table_number"><?php echo h($food['protein']); ?></td>
                <td id="table_number"><?php echo h($food['servings']) ?></td>
                <td>
                    <form action="<?php echo url_for('/users/diet/remove.php')?>"
                          method="POST">
                        <input type="hidden" name="id" value="<?php echo h($food['item_id'])?>">
                        <input type="submit" name="remove" value="Remove" id="table_button"/>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>

    <?php

} else {
    echo "<p>You have no food added.</p>";
}
?>


<?php include(SHARED_PATH . '/footer.php'); ?>