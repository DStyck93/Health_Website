<?php global $time_frame ?>

<div class="timeframe_nav">
    <a href="index.php?tf=day" class="timeframe_nav"
        <?php if($time_frame =='day' || $time_frame == '') { echo 'style=\'color: darkcyan;\''; }?>>Daily
    </a>&nbsp;|

    <a href="index.php?tf=week" class="timeframe_nav"
        <?php if($time_frame =='week') { echo 'style=\'color: darkcyan;\''; }?>>Weekly
    </a>&nbsp;|

    <a href="index.php?tf=month" class="timeframe_nav"
        <?php if($time_frame =='month') {echo 'style=\'color: darkcyan;\''; }?>>Monthly
    </a>
</div>