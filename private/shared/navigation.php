<?php global $id; ?>

<div id="nav_bar">
    <a href="<?php echo url_for('users/index.php') ?>">Home</a>&nbsp;&nbsp;&nbsp;
    <a href="<?php echo url_for('users/diet/index.php') ?>">Diet</a>&nbsp;&nbsp;&nbsp;
    <a href="<?php echo url_for('users/exercise/index.php') ?>">Exercise</a>&nbsp;&nbsp;&nbsp;
    <a href="<?php echo url_for('users/profile/index.php') ?>">Profile</a>&nbsp;&nbsp;&nbsp;
    <a href="<?php echo url_for('logout.php') ?>">Sign Out</a>
</div>
