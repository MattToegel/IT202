<?php
$response = array("status" => 400, "message" => "Error saving score");
require(__DIR__ . "/../lib/helpers.php");
$rating = .01;
//TODO include new field for interest calc so you can batch sets of requests (like 100 per invocation)
$query = "SELECT points, user_id from tfp_userstats where points > 0 LIMIT 100";
$db = getDB();
$stmt = $db->prepare($query);
$r = $stmt->execute();
$total = $stmt->rowCount();
if($r){
    $results = $stmt->fetchAll();
    if($results){
        foreach($results as $user){
            $id = safe_get($user, "user_id", -1);
            $points = (int)safe_get($user, "points", 0);
            if($points < 0){
                $points = 0;
            }
            $interest = ceil($points * ($rating/365));
            changePoints($id, $interest, "You've earned interest, tanks for saving!");
        }
    }
}
$response["status"] = 200;
$response["message"] = "Calculated interest for $total players";
echo json_encode($response);
?>