<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div class="container-fluid">
    <h1>Home</h1>
    <div class="lead text-center mb-3">
        Welcome to Rescue Mission! <br>
        This game is inspired by Wumpus World / Hunt the Wumpus with my own take on it. <br>
        The goal is to traverse as many levels of the Wolf's Den as possible by finding ladders.<br>
        On the way, you may rescue friends for extra points.<br>
        If you run into the Wolf or fall into one of the many pits throughout, you'll lose and get brought back to the first level.<br>
        As you play, you may earn points to use in the Shop to acquire helpful items to push further through the Den.<br>
        You must have an account and be logged in for your score/attempt to persist and be recorded.<br>
        Good luck!
        <br><br>
        References: <a href="https://en.wikipedia.org/wiki/Hunt_the_Wumpus">Hunt the Wumpus</a>
    </div>
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