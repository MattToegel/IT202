<?php
include_once(__DIR__."/partials/header.partial.php");
//Used as a redirect page to show win or loss. Resets some session vars as part of anti-cheat system
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
}
?>
<div>
    <?php $outcome = Common::get($_SESSION, "outcome", false);?>
    <?php if($outcome):?>
        <?php unset($_SESSION["outcome"]);?>
        <?php unset($_SESSION["started"]);?>
        <?php if($outcome == "win"):?>
            <h1>Congrats, you won this round!</h1>
        <?php else:?>
            <h1>Better luck next time. Tanks for Playing : )</h1>
        <?php endif;?>
    <?php else:?>
    <p>Why are you here? There are no game outcomes to display</p>
    <?php endif;?>
    <div>
        <p>Try again?</p>
        <a href="game.php">Let's go!</a>
    </div>
</div>
