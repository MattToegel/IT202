<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
$testing = false;
if (isset($_GET["test"])) {
    $testing = true;
}

//TODO check if user can afford
//get number of eggs in ownership
//first egg is free
//each egg extra is base_cost * #_of_eggs
//$eggs_owned = 0;
//$base_cost = 10;
//$cost = $eggs_owned * $base_cost;
$cost = calcNextEggCost();
if ($cost < 0) {
    $response = ["status" => 400, "error" => "Error calculating cost"];
    echo json_encode($response);
    die();
}
if ($cost > getBalance()) {
    $response = ["status" => 400, "error" => "You can't afford this right now"];
    echo json_encode($response);
    die();
}
//super secret egg-generator
$egg = [
    "name" => "Egg",
    "base_rate" => mt_rand(0, 5),
    "mod_min" => mt_rand(1, 19),
    "state" => 0,
    "user_id" => get_user_id()
];
//since this value depends on mod_min we can't quite initialized it all at once
$egg["mod_max"] = mt_rand($egg["mod_min"], 20);

//https://www.w3schools.com/php/func_math_mt_rand.asp
$total = $egg["base_rate"] + $egg["mod_min"] + $egg["mod_max"];
$max = 45;
$percent = $total / $max;
//TODO egg base_rate, mod min/max should increase the time of hatching
//Incubator stats should reduce time of hatching
//$eggTypes = ["Ancient", "Legendary", "Rare", "Uncommon", "Common"];
$eggTypes = ["Common", "Uncommon", "Rare", "Legendary", "Ancient"];
$index = (int)(count($eggTypes) * $percent);
$egg["name"] = $eggTypes[$index] . " Egg";

//https://www.delftstack.com/howto/php/how-to-add-days-to-date-in-php/
//https://stackoverflow.com/a/1286272
$nst = new DateTime();
$days = $egg["base_rate"] + mt_rand($egg["mod_min"], $egg["mod_max"]);
$nst->add(new DateInterval("P" . $days . "D"));
$nst = $nst->format("Y-m-d H:i:s");

if ($testing) {
    echo "<br>+$days<br>";
}
$egg["next_stage_time"] = $nst;
if (!$testing) {
    $db = getDB();
    $stmt = $db->prepare("SELECT MAX(id) as max from F20_Eggs");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $max = (int)$result["max"];
    $max++;
    $egg["name"] .= " #$max";//forgot name was unique so just appending the "expected" id
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
        $response = ["status" => 200, "egg" => $egg];
        echo json_encode($response);
        die();
    }
    else {
        $e = $stmt->errorInfo();
        $response = ["status" => 400, "error" => $e];
        echo json_encode($response);
        die();
    }
}
else {
    echo "<pre>" . var_export($egg, true) . "</pre>";
}

?>
