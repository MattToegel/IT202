<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div class="container-fluid">
    <h1>Home</h1>
    <?php

    /*if (is_logged_in(true)) {
    //echo "Welcome home, " . get_username();
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}*/
    ?>

    <?php
    //this is day which is the default
    require(__DIR__ . "/../../partials/scores_table.php");
    ?>
    <?php
    $duration = "week";
    require(__DIR__ . "/../../partials/scores_table.php");
    ?>
    <?php
    $duration = "month";
    require(__DIR__ . "/../../partials/scores_table.php");
    ?>
    <?php
    $duration = "lifetime";
    require(__DIR__ . "/../../partials/scores_table.php");
    ?>
</div>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>