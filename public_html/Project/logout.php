<?php
session_start();
session_unset();
session_destroy();
//setcookie("PHPSESSID", "", time()-3600);
session_start();
require_once(__DIR__ . "/../../lib/functions.php");
flash("You have been logged out", "success");
die(header("Location: login.php"));
