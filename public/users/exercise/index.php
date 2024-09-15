<?php
require_once('../../../private/initialize.php');
require_login();

$page_title = 'Exercise';
include(SHARED_PATH . '/header.php');
?>

<h1>Exercise (Coming Soon!)</h1> <!-- TODO - Edit header -->

<?php include(SHARED_PATH . '/navigation.php'); ?>

<div id="row">
    <div id="column">
        <h2>Daily</h2>
        <h3>Calories Burned:</h3>
        <h3>Active Time:</h3>
        <h3>Steps:</h3><br>
    </div>

    <div id="column">
        <h2>Weekly</h2>
        <h3>Calories Burned:</h3>
        <h3>Active Time:</h3>
        <h3>Steps:</h3><br>
    </div>

    <div id="column">
        <h2>Monthly</h2>
        <h3>Calories Burned:</h3>
        <h3>Active Time:</h3>
        <h3>Steps:</h3><br>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
