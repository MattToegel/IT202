<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div class="container-fluid">
    <h1>Home</h1>
    <?php
    /*if (is_logged_in(true)) {
    echo "Welcome, " . get_username();
} else {
    echo "You're not logged in";
}*/
    //shows session info
    //echo "<pre>" . var_export($_SESSION, true) . "</pre>";
    $duration = "week";
    ?>
    <?php require(__DIR__ . "/../../partials/score_table.php"); ?>
</div>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>