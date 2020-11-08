<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
//TODO check if user can afford

//super secret egg-generator
$egg = [
    "name" => "Egg",
    "base_rate" => mt_rand(0, 5),
    "mod_min" => mt_rand(1, 20),
    "mod_max" => mt_rand(1, 20),
    "state" => 0,
    "user_id" => get_user_id()];
//https://www.w3schools.com/php/func_math_mt_rand.asp


$db = getDB();
$nst = date('Y-m-d H:i:s');//calc
$days = $egg["base_rate"] + mt_rand($egg["mod_min"], $egg["mod_max"]);
$day_string = $days == 1 ? "+1 day" : "+$days days";
$nst = date('Y-m-d H:i:s', strtotime($day_string, $nst));
$egg["next_stage_time"] = $nst;
$user = get_user_id();
$stmt = $db->prepare("INSERT INTO F20_Eggs (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
$r = $stmt->execute([
    ":name" => $egg["name"],
    ":state" => $egg["state"],
    ":br" => $egg["base_rate"],
    ":min" => $egg["mod_min"],
    ":max" => $egg["mod_max"],
    ":nst" => $egg["next_stage_time"],
    ":user" => $egg["user_id"]
]);
if ($r) {
    echo json_encode($egg);
    die();
}
else {
    $e = $stmt->errorInfo();
    echo json_encode($e);
    die();
}
?>