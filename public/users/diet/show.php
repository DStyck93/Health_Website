<?php
require_once('../../../private/initialize.php');
require_login();

global $errors;

$food_set = find_food_by_user($_SESSION['user_id']);

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
include(SHARED_PATH . '/user_header.php');
?>

<p id="back_button"><a href="<?php echo url_for('/users/diet/index.php') ?>">&laquo; Back</a></p>

<h1>Your Food</h1>

<?php
include(SHARED_PATH . '/navigation.php');

echo display_errors($errors);
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}

if (!empty($food_set)) { ?>

    <table class="list">
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Calories</th>
            <th>Fat</th>
            <th>Carb</th>
            <th>Protein</th>
            <th>Serving Size</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($food_set as $food) { ?>
            <tr>
                <td><?php echo h($food['date_added'])?></td>
                <td><?php echo h($food['food_name']); ?></td>
                <?php $macros = array('fat' => $food['fat'], 'carb' => $food['carb'], 'protein' => $food['protein']) ?>
                <td><?php echo h(calculate_calories($macros))?></td>
                <td><?php echo h($food['fat']); ?></td>
                <td><?php echo h($food['carb']); ?></td>
                <td><?php echo h($food['protein']); ?></td>
                <td><?php echo h($food['serving_description']); ?></td>
                <td>
                    <form action="<?php echo url_for('/users/diet/remove.php?id=' . h(u($food['item_id'])))?>"
                          method="post">
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

include(SHARED_PATH . '/footer.php');
?>