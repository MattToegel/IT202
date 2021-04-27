<?php
require(__DIR__ . "/../lib/helpers.php");
$response = array("status"=>400, "message"=>"something went wrong");
if(is_logged_in(false)) {

    $playerTanks = safe_get($_SESSION["user"], "tanks", []);
    if (count($playerTanks) > 0) {
        $tanks = array();
        //get first/only tank
        $t = $playerTanks[0];
        error_log(var_export($t, true));
        $speed = (int)safe_get($t, "speed", 50);
        $range = (int)safe_get($t, "range", 50);
        $turnSpeed = (int)safe_get($t, "turnSpeed", 25);
        $fireRate = (int)safe_get($t, "fireRate", 10);
        $health = (int)safe_get($t, "health", 3);
        $damage = (int)safe_get($t, "damage", 1);
        $playerTank = array(
            "speed"=>$speed,
            "range"=>$range,
            "turnSpeed"=>$turnSpeed,
            "fireRate"=>$fireRate,
            "health"=>$health,
            "tankColor"=>safe_get($t, "tankColor"),
            "barrelColor" =>safe_get($t, "barrelColor"),
            "barrelTipColor" =>safe_get($t, "barrelTipColor"),
            "treadColor" => safe_get($t, "treadColor"),
            "hitColor" => safe_get($t, "hitColor"),
            "gunType" => (int)safe_get($t, "gunType",1),
            "damage"=>$damage
        );
        array_push($tanks, $playerTank);
        //https://www.w3schools.com/php/func_math_mt_rand.asp

        $playerLevel = safe_get($_SESSION["user"],"level",1);
        //07-14-2020 added logic to take the max value between playerTank health and playerLevel
        //this is to mitigate any strategy where a player keeps their tank health low and attempts to win with 1 shots
        $enemyTank = array(
            "speed"=>mt_rand($speed*.5, $speed*1.5),
            "range"=>mt_rand($range*.5, $range*1.5),
            "turnSpeed"=>mt_rand($turnSpeed*.5, $turnSpeed*1.5),
            "fireRate"=>mt_rand($fireRate*.5, $fireRate*1.5),
            "health"=>max(mt_rand($health*.5, $health*1.5), $playerLevel * 3),
            "tankColor"=>"#" . dechex(rand(0x000000, 0xFFFFFF)),
            "barrelColor" =>"#" . dechex(rand(0x000000, 0xFFFFFF)),
            "barrelTipColor" => "#" . dechex(rand(0x000000, 0xFFFFFF)),
            "treadColor" => "#" . dechex(rand(0x000000, 0xFFFFFF)),
            "hitColor" => '#A2082B',
            "gunType" => mt_rand(1,3),
            "damage"=>max(mt_rand($damage*.5, $damage*1.5),1)
        );
        array_push($tanks, $enemyTank);
        $response["status"] = 200;
        $response["tanks"] = $tanks;
        $response["message"] = "Tanks acquired";
    }
    else{
        $response["message"] = "Player doesn't have any tanks";
    }
}
echo json_encode($response);
?>