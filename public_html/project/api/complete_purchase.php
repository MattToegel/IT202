<?php
$response = array("status"=>400, "message"=>"Unknown error");
if(isset($_POST["order"])){
    $order = $_POST["order"];
    $order = json_decode($order);
    try {
        require(__DIR__ . "/../includes/common.inc.php");
        if (Common::is_logged_in(false)) {
            $points = (int)Common::get($_SESSION["user"], "points", 0);
            $cost = 0;
            foreach ($order as $item) {
                $c = (int)$item["cost"];
                $q = (int)$item["quantity"];
                $cost += ($c * $q);
            }
            if ($cost <= $points) {
                //do purchase
                $points -= $cost;
                $_SESSION["user"]["points"] = $points;//update this live, if out of sync it'll be handled later
                $response["status"] = 200;
                $response["message"] = "Purchase complete";
            } else {
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