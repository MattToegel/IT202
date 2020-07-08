<?php
$response = array("status"=>400, "message"=>"Unknown error");
if(isset($_POST["order"])){
    $order = $_POST["order"];
    $order = json_decode($order, true);
    try {
        require(__DIR__ . "/../includes/common.inc.php");
        if (Common::is_logged_in(false)) {
            //regarding Note below: I could call the aggregate stats to refresh the points here
            //if I'm concerned about the points not being accurate/updated
            $points = (int)Common::get($_SESSION["user"], "points", 0);
            $cost = 0;
            $quantity = 0;
            foreach ($order as $item) {
                $c = (int)$item["cost"];
                $q = (int)$item["quantity"];
                $quantity += $q;
                $cost += ($c * $q);
            }
            //make sure cost is not free or negative
            //make sure it's at least the same as quantity (helps reduce, not eliminates, the need to check our Items table for confirmation)
            //make sure we can afford
            //Note: technically should check db for user's points, but I'm assuming session should be accurate enough
            //your projects shouldn't make such assumptions
            if ($cost > 0 && $cost >= $quantity && $cost <= $points) {
                //do purchase
                //TODO should really validate that the ordered items match what's in the DB
                //can be done either 1 by 1 or by using an IN clause, but it requires special crafting for PDO
                //since it's not breaking data if something gets corrupted in my scenario I'm going to omit the check
                $user_id = Common::get_user_id();
                $response = DBH::save_order($order);
                if(Common::get($response, "status", 400) == 200) {
                    $sysid = Common::get_system_id();
                    //negative cost since we're spending
                    $response = DBH::changePoints($user_id, -$cost, $sysid, "purchase", "shop");
                    if(Common::get($response, "status", 400) == 200) {
                        $points -= $cost;
                        //update tank
                        $playerTanks = Common::get($_SESSION["user"], "tanks", []);
                        if (count($playerTanks) > 0) {
                            $t = $playerTanks[0];
                            $speed = (int)Common::get($t, "speed", 50);
                            $range = (int)Common::get($t, "range", 50);
                            $turnSpeed = (int)Common::get($t, "turnSpeed", 25);
                            $fireRate = (int)Common::get($t, "fireRate", 10);
                            $health = (int)Common::get($t, "health", 3);
                            $damage = (int)Common::get($t, "damage", 1);
                            foreach ($order as $item) {
                                $type = (int)$item["type"];
                                $q = (int)$item["quantity"];
                                if($type == "speed"){
                                    $speed += $q;
                                }
                                else if($type == "range"){
                                    $range += $q;
                                }
                                else if($type == "turnSpeed"){
                                    $turnSpeed += $q;
                                }
                                else if($type == "fireRate"){
                                    $fireRate += $q;
                                }
                                else if($type == "health"){
                                    $health += $q;
                                }
                                else if($type=="damage"){
                                    $damage += $q;
                                }
                            }
                            $t["speed"] = $speed;
                            $t["range"] = $range;
                            $t["turnSpeed"] = $turnSpeed;
                            $t["fireRate"] = $fireRate;
                            $t["health"] = $health;
                            $result = DBH::update_tank($t);
                            if(Common::get($result, "status", 400) == 200) {
                                $result = DBH::get_tanks(Common::get_user_id());
                                if (Common::get($result, "status", 400) == 200) {
                                    $tanks = Common::get($result, "data", []);
                                    $_SESSION["user"]["tanks"] = $tanks;
                                }
                            }
                        }
                        $_SESSION["user"]["points"] = $points;//update this live, if out of sync it'll be handled later
                        $response["status"] = 200;
                        $response["message"] = "Purchase complete";
                    }
                }
            }
            else {
                $response["message"] = "You don't have enough points";
            }
        }
    }
    catch(Exception $e){
        error_log($e->getMessage());
    }
}
echo json_encode($response);
?>