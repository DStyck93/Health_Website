<?php
require_once('../../../private/initialize.php');
require_login();
global $errors;
$page_title = 'Diet';
include(SHARED_PATH . '/header.php');

if (is_post_request()) {
    $food[] = '';
    $food['name'] = $_POST['name'] ?? '';
    $food['calories'] = $_POST['calories'] ?? 0;
    $food['carbs'] = $_POST['carbs'] ?? 0;
    $food['fat'] = $_POST['fat'] ?? 0;
    $food['protein'] = $_POST['protein'] ?? 0;
    $food['servings'] = $_POST['servings'] ?? 1.0;

    if ($food['name'] == '') {
        $errors[] = 'Please enter a name';
    } else if (strlen($food['name']) > 255) {
        $errors[] = 'Name must be less than 256 characters';
    }

    if(empty($errors)) {
        if (add_custom_food($food)) {
            $_SESSION['message'] = $food['name'] . " has been added.";
            redirect_to(url_for('/users/diet/index.php'));
        } else {
            $errors[] = 'An error occurred while adding the food.';
        }
    }
} else {
    $food[] = '';
    $food['name'] = '';
    $food['calories'] = 0;
    $food['carbs'] = 0;
    $food['fat'] = 0;
    $food['protein'] = 0;
    $food['servings'] = 1.0;
}
?>

<!-- Page Header -->
<h1>Custom Food</h1>
<?php include(SHARED_PATH . '/navigation.php');?>

<!-- Back Link -->
<p id="back_button"><a href="<?php echo url_for('/users/diet/index.php'); ?>">&laquo; Back</a></p>

<!-- Messages -->
<?php echo display_errors($errors);?>
<br><p><?php display_message() ?></p>
<p>Enter the nutritional value for 1 serving.</p>

<form action="custom.php" method="POST">
    <dl><dt id="custom_food">Name</dt>
        <dd><label><input type="text" name="name" value="<?php echo h($food['name']) ?>"/></label></dd>
    </dl>
    <dl><dt id="custom_food">Calories</dt>
        <dd><label><input type="number" name="calories" value="<?php echo h($food['calories']) ?>"/></label></dd>
    </dl>
    <dl><dt id="custom_food">Carbs (g)</dt>
        <dd><label><input type="number" name="carbs" value="<?php echo h($food['carbs']) ?>"/></label></dd>
    </dl>
    <dl><dt id="custom_food">Fat (g)</dt>
        <dd><label><input type="number" name="fat" value="<?php echo h($food['fat']) ?>"/></label></dd>
    </dl>
    <dl><dt id="custom_food">Protein (g)</dt>
        <dd><label><input type="number" name="protein" value="<?php echo h($food['protein']) ?>"/></label></dd>
    </dl>
    <dl><dt id="servings">Servings</dt>
        <dd><label><input type="range" min='0.5' max='10' step='0.5' oninput="range_value.innerText = this.value"
                          name="servings" class="slider" value="<?php echo h($food['servings']) ?>"/></label>
            <p id="range_value"><?php echo h($food['servings']) ?></p>
        </dd>
    </dl>
    <input type="submit" value="Add Food" id="add_food_button"/>
</form>

<?php include(SHARED_PATH . '/footer.php'); ?>
