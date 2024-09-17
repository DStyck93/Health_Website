<?php
require_once('../../../private/initialize.php');
require_login();
global $errors;

if (!is_post_request()) {redirect_to(url_for('/users/diet/add.php'));}

$id = $_POST["id"] ?? '';
$food = find_food_by_id($id);

$page_title = 'Diet';
include(SHARED_PATH . '/header.php');
?>

<!-- Page Header -->
<h1>Serving Size</h1>
<?php include(SHARED_PATH . '/navigation.php');?>

<!-- Back Link -->
<p id="button"><a href="<?php echo url_for('/users/diet/add.php') ?>">&laquo; Back</a></p>

<!-- Messages -->
<?php echo display_errors($errors);?>
<p><?php display_message() ?></p>
<p>Confirm your serving size and then press Add Food.</p>

<!-- Food Info -->
<h2><?php echo $food['food_name'] ?></h2>
<p>Calories: <?php echo h(calculate_calories($food))?></p>
<p>Carbs: <?php echo h($food['carb']) ?></p>
<p>Fats: <?php echo h($food['fat']) ?></p>
<p>Protein: <?php echo h($food['protein']) ?></p>
<p>Serving Size (g): <?php echo h($food['serving_weight']) ?></p>
<p>Serving Description: <?php echo h($food['serving_description']) ?></p>

<!-- Form -->
<form action="<?php echo url_for('/users/diet/post.php') ?>"
      method="post">
    <input type="hidden" name="id" value="<?php echo h($id); ?>">
    <dl>
        <dt id="servings">Servings</dt>
        <dd><label><input type="range" min='0.5' max='10' step='0.5' oninput="range_value.innerText = this.value"
                          name="servings" class="slider" value="1"/></label>
            <p id="range_value">1</p>
        </dd>
    </dl>
    <input type="submit" value="Add Food" id="add_food_button"/>
</form>


<?php include(SHARED_PATH . '/footer.php'); ?>
