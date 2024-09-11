<?php
require_once('../../../private/initialize.php');
require_login();

global $errors;

if (is_get_request()) {
    $food_name = $_GET['search'] ?? '';

    if ($food_name != '') {
        $food_set = find_food_by_name($food_name);
    }
}

$page_title = 'Diet';
include(SHARED_PATH . '/user_header.php');
?>

<p><a href="<?php echo url_for('/users/diet/index.php') ?>">&laquo; Back</a></p>

<h1>Add Food</h1>

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
?>

<!-- Lookup Table -->
<form action="<?php echo url_for('users/diet/add.php')?>" method="GET">
    <label><input name='search' id="search_bar" type="search" placeholder="Lookup Food"/></label>
    <input id="search_button" type="submit" value="Search"/>
</form>

<?php if (!empty($food_set)) { ?>

    <table class="list">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Group</th>
            <th>Fat</th>
            <th>Carb</th>
            <th>Protein</th>
            <th>Serving Size</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($food_set as $food) { ?>
            <tr>
                <td><?php echo h($food['food_id'])?></td>
                <td><?php echo h($food['food_name']); ?></td>
                <td><?php echo h($food['food_group']); ?></td>
                <td><?php echo h($food['fat']); ?></td>
                <td><?php echo h($food['carb']); ?></td>
                <td><?php echo h($food['protein']); ?></td>
                <td><?php echo h($food['serving_description']); ?></td>
                <td><a href="<?php echo url_for('/users/diet/post.php?id=' . h(u($food['food_id']))); ?>">Add</a></td>
            </tr>
        <?php } ?>

    </table>

<?php
    mysqli_free_result($food_set);
}

include(SHARED_PATH . '/footer.php');
?>