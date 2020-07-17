<?php
include_once(__DIR__."/partials/header.partial.php");
//Used as a redirect page to show win or loss. Resets some session vars as part of anti-cheat system
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
}
?>
<div class="jumbotron">
    <?php $outcome = Common::get($_SESSION, "outcome", false);?>
    <?php if($outcome):?>
        <?php unset($_SESSION["outcome"]);?>
        <?php unset($_SESSION["started"]);?>
        <?php if($outcome == "win"):?>
            <h1 class="display-5">Congrats, you won this round!</h1>
        <?php else:?>
            <h1 class="display-5">Better luck next time. Tanks for Playing : )</h1>
        <?php endif;?>
    <?php else:?>
        <p class="lead">Hmm, either the match ended too quickly or you stumbled here by mistake.</p>
    <?php endif;?>
    <div class="container">
        <p class="lead">Try again?</p>
        <a class="btn btn-secondary" href="game.php">Let's go!</a>
    </div>
</div>