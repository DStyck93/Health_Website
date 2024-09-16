<?php
require_once('../../../private/initialize.php');
require_login();

global $errors;

$time_frame = $_GET['tf'] ?? '';

// Get user nutrition data
$nutrition_set = get_user_nutrition($_COOKIE['user_id'], $time_frame);
$total_nutrition = calculate_nutrition($nutrition_set);
$calories = calculate_calories($total_nutrition);

// User's food
$food_set = find_food_by_user($time_frame);

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

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
echo "</br><p>" . display_message() . "</p>";
?>

<!-- Timeframe Selector -->
<?php include(SHARED_PATH . '/timeframe_selector.php'); ?>

<!-- Nutrition Numbers -->

<?php if($time_frame =='day') { ?><h2>Daily</h2>
<?php } else if($time_frame == 'week' || $time_frame == '') { ?><h2>Weekly</h2>
<?php } else { ?><h2>Monthly</h2><?php } ?>

<h3>Calories: <?php echo h($calories) ?></h3>
<h4>Carbs: <?php echo h($total_nutrition['carb']) ?></h4>
<h4>Fats: <?php echo h($total_nutrition['fat']) ?></h4>
<h4>Protein: <?php echo h($total_nutrition['protein']) ?></h4>

<!-- Add Food Button -->
<form action="<?php echo url_for('/users/diet/add.php'); ?>">
    <input type="submit" value="Add Food" id="add_food_button"/>
</form>

<!-- User Food Table -->
<h2 id="table_title">Your Food</h2>
<?php
if (!empty($food_set)) { ?>

    <table class="list">
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Group</th>
            <th id="table_number">Calories</th>
            <th id="table_number">Carbs</th>
            <th id="table_number">Fats</th>
            <th id="table_number">Protein</th>
            <th id="table_number">Servings</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($food_set as $food) { ?>
            <tr>
                <td>
                    <?php
                    $formatted_date = date("m/d\nH:s", strtotime(h($food['date_added'])));
                    echo nl2br(h($formatted_date));
                    ?>
                </td>
                <td id="long_text"><?php echo h($food['food_name']); ?></td>
                <td id="food_group"><?php echo h($food['food_group']); ?></td>
                <td id="table_number"><?php echo calculate_calories($food)?></td>
                <td id="table_number"><?php echo h($food['carb']); ?></td>
                <td id="table_number"><?php echo h($food['fat']); ?></td>
                <td id="table_number"><?php echo h($food['protein']); ?></td>
                <td id="table_number"><?php echo h($food['servings']) ?></td>
                <td>
                    <form action="<?php echo url_for('/users/diet/remove.php')?>"
                          method="POST">
                        <input type="hidden" name="id" value="<?php echo h($food['item_id'])?>">
                        <input type="submit" name="remove" value="Remove"/>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>

    <?php
    mysqli_free_result($food_set);

} else {
    echo "<p>You have no food added.</p>";
}
?>


<?php
mysqli_free_result($nutrition_set);

include(SHARED_PATH . '/footer.php');
?>