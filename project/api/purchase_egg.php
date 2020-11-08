<?php
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
//TODO check if user can afford

//super secret egg-generator
$name = "Egg";
//https://www.w3schools.com/php/func_math_mt_rand.asp
$base = mt_rand(0, 5);
$mod_min = mt_rand(-20, 20);
$mod_max = mt_rand(1, 20);
$state = 0;
?>
