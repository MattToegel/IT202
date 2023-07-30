<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php

if (is_logged_in(false)) {
    //echo "Welcome home, " . get_username();
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}
?>
<div class="container-fluid">
    <div class="h-50 p-5 text-bg-dark rounded-3">
        <h1>Welcome to the Cat Adoption Center!</h1>
        <p>Thank you for your interest in our mission. We are a non-profit organization dedicated to rescuing and rehoming stray and abandoned cats. Our goal is to ensure that every cat has a loving and permanent home.</p>
        <p>Our adoption process is designed to ensure the best possible match between our cats and their new families. We also offer a fostering program for those who are not ready to commit to adoption but still want to help. Fostering a cat can be a rewarding experience and it greatly helps us in our mission to save as many cats as possible.</p>
        <p>Visit our adoption and fostering pages for more information. We look forward to helping you find your new feline friend!</p>
        <p class="text-center"><a class="btn btn-primary btn-lg" href="<?php get_url("browse.php", true); ?>" role="button">Find your next fuzzy friend</a></p>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../partials/footer.php");
?>