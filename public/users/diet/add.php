<?php
require_once('../../../private/initialize.php');
require_login();

global $errors;

// Search button clicked
if (is_post_request()) {
    $food_name = $_POST['search'] ?? '';
    if ($food_name != '') {
        $food_set = find_food_by_name($food_name);
        if (mysqli_num_rows($food_set) == 0) {$errors[] = "No food found with that description.";}
    }
} else {
    $_SESSION['message'] = "Select food to add";
}

$page_title = 'Diet';
include(SHARED_PATH . '/header.php');
?>

<!-- Page Header -->
<h1>Add Food</h1>
<?php include(SHARED_PATH . '/navigation.php');?>

<!-- Back Link -->
<p id="back_button"><a href="<?php echo url_for('/users/diet/index.php') ?>">&laquo; Back</a></p>

<!-- Messages -->
<?php echo display_errors($errors);?>
<p><?php display_message() ?></p>
<?php if(!empty($food_set) && mysqli_num_rows($food_set) > 0) { ?>
    <p>Click the Add button to adjust your serving size.</p>
<?php } ?>

<!-- Custom Food Button -->
<form action="<?php echo url_for('users/diet/custom.php')?>">
    <input id="custom_food_button" type="submit" value="Add Custom Food"/>
</form>

<!-- Search Bar -->
<form action="<?php echo url_for('users/diet/add.php')?>" method="POST" id="search_bar">
    <label><input name='search' id="search_bar" type="search" placeholder="Lookup Food"/></label>
    <input id="search_button" type="submit" value="Search"/>
</form>

<!-- Search Table -->
<?php if (!empty($food_set) && mysqli_num_rows($food_set) > 0) { ?>

    <table class="list">
        <tr>
            <th>Name</th>
            <th>Group</th>
            <th id="table_number">Calories</th>
            <th id="table_number">Carbs</th>
            <th id="table_number">Fats</th>
            <th id="table_number">Protein</th>
            <th id="table_number">Serving Size (g)</th>
            <th>Serving Description</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($food_set as $food) { ?>
            <tr>
                <td><?php echo h($food['food_name']); ?></td>
                <td><?php echo h($food['food_group']); ?></td>
                <td id="table_number"><?php echo h(calculate_calories($food))?></td>
                <td id="table_number"><?php echo h($food['carb']); ?></td>
                <td id="table_number"><?php echo h($food['fat']); ?></td>
                <td id="table_number"><?php echo h($food['protein']); ?></td>
                <td id="table_number">
                    <?php
                    if ($food['serving_weight'] != 0) {
                        echo h($food['serving_weight']);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td id="long_text">
                    <?php
                    if ($food['serving_description'] != 0) {
                        echo h($food['serving_description']);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td id="table_number">
                    <form action="<?php echo url_for('/users/diet/serving.php')?>"
                          method="POST">
                        <input type="hidden" name="id" value="<?php echo h($food['food_id']); ?>">
                        <input type="submit" name="add" value="Add" id="table_button"/>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>

<?php
    mysqli_free_result($food_set);
}

include(SHARED_PATH . '/footer.php');
?>