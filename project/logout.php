<?php
session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
?>
<?php require_once(__DIR__ . "/partials/nav.php");/*ultimately, this is just here for the function to be loaded now*/ ?>
<?php

flash("You have been logged out");
die(header("Location: login.php"));
?>
