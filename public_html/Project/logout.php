<?php
session_start();
session_unset();
session_destroy();
//setcookie("PHPSESSID", "", time()-3600);
require_once(__DIR__ . "/../../partials/nav.php");
flash("You have been logged out", "success");
//echo "<pre>" . var_export($_SESSION, true) . "</pre>";
die(header("Location: login.php"));
