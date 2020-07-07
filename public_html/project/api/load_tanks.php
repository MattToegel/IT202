<?php
require(__DIR__ . "/../includes/common.inc.php");
$response = array("status"=>400, "message"=>"something went wrong");
if(Common::is_logged_in(false)) {

    $playerTanks = Common::get($_SESSION["user"], "tanks", []);
    if (count($playerTanks) > 0) {
        $tanks = array();
        //get first/only tank
        $t = $playerTanks[0];
        error_log(var_export($t, true));
        $speed = (int)Common::get($t, "speed", 50);
        $range = (int)Common::get($t, "range", 50);
        $turnSpeed = (int)Common::get($t, "turnSpeed", 25);
        $fireRate = (int)Common::get($t, "fireRate", 10);
        $health = (int)Common::get($t, "health", 3);
        $playerTank = array(
            "speed"=>$speed,
            "range"=>$range,
            "turnSpeed"=>$turnSpeed,
            "fireRate"=>$fireRate,
            "health"=>$health,
            "tankColor"=>Common::get($t, "tankColor"),
            "barrelColor" =>Common::get($t, "barrelColor"),
            "barrelTipColor" =>Common::get($t, "barrelTipColor"),
            "treadColor" => Common::get($t, "treadColor"),
            "hitColor" => Common::get($t, "hitColor"),
            "gunType" => (int)Common::get($t, "gunTypr",1)
        );
        array_push($tanks, $t);
        //https://www.w3schools.com/php/func_math_mt_rand.asp


        $enemyTank = array(
            "speed"=>mt_rand($speed*.5, $speed*1.5),
            "range"=>mt_rand($range*.5, $range*1.5),
            "turnSpeed"=>mt_rand($turnSpeed*.5, $turnSpeed*1.5),
            "fireRate"=>mt_rand($fireRate*.5, $fireRate*1.5),
            "health"=>mt_rand($health*.5, $health*1.5),
            "tankColor"=>"#" . dechex(rand(0x000000, 0xFFFFFF)),
            "barrelColor" =>"#" . dechex(rand(0x000000, 0xFFFFFF)),
            "barrelTipColor" => "#" . dechex(rand(0x000000, 0xFFFFFF)),
            "treadColor" => "#" . dechex(rand(0x000000, 0xFFFFFF)),
            "hitColor" => '#A2082B',
            "gunType" => mt_rand(1,3)
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

