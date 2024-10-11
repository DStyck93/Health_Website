<?php
require_once('../../../../private/initialize.php');
require_login();

// Search button clicked
if (is_post_request()) {
    $activity_description = $_POST['search'] ?? '';
    $activity_type = $_POST['activity_type'] ?? 'All';
    $activity_set = find_activities($activity_description, $activity_type);
    if (mysqli_num_rows($activity_set) == 0) {$errors[] = "No Activity found with that description.";}
}

$page_title = 'Exercise';
include(SHARED_PATH . '/header.php');
?>

<!-- Page Title -->
<h1>Add Activity</h1>

<!-- Nav Bar -->
<?php include(SHARED_PATH . '/navigation.php'); ?>

<!-- Messages -->
<?php echo "<br>" . display_errors($errors);?>
<p><?php display_message() ?></p>

<!-- Search Function -->
<form action="<?php echo url_for('users/exercise/add.php')?>" method="POST" id="search_bar">
    <!-- Search Bar -->
    <input name='search' id="search_bar" type="search" placeholder="Lookup Activity"/>
    <input id="search_button" type="submit" value="Search"/><br>

    <!-- Activity Type -->
    <dl><dt id="activity_type_selector">Activity Type</dt>
    <dd><select name="activity_type">
        <option value="All" selected="selected">All</option>
        <option value="Bicycling">Bicycling</option>
        <option value="Conditioning">Conditioning</option>
        <option value="Walking & Running">Walking & Running</option>
        <option value="Sports">Sports</option>
        <option value="Water">Water</option>
        <option value="Miscellaneous">Misc.</option>
    </select></dd></dl>
</form>

<!-- Search Table -->
<?php if (!empty($activity_set) && mysqli_num_rows($activity_set) > 0) { ?>

    <table class="list">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th id="table_number">MET</th>
            <th>&nbsp;</th>
        </tr>

        <?php foreach ($activity_set as $activity) { ?>
            <tr>
                <td><?php echo h($activity['activity_type']); ?></td>
                <td><?php echo h($activity['activity_description']); ?></td>
                <td id="table_number"><?php echo h($activity['MET']); ?></td>
                <td id="table_number">
                    <form action="<?php echo url_for('/users/exercise/details.php')?>"
                        method="POST">
                        <input type="hidden" name="id" value="<?php echo h($activity['activity_code']); ?>">
                        <input type="submit" name="add" value="Add" id="table_button"/>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>

    <?php mysqli_free_result($activity_set); 
}?>

<!-- Footer -->
<?php include(SHARED_PATH . '/footer.php'); ?>