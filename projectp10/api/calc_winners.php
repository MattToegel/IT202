<?php
$response = array("status" => 400, "message" => "Error saving score");
require(__DIR__ . "/../lib/helpers.php");
$query = "SELECT * from tfp_competitions c WHERE expires <= current_timestamp() and calced_winner != 1 and participants >= min_participants";
$db = getDB();
$stmt = $db->prepare($query);
$r = $stmt->execute();
$processed = 0;
if ($r) {
    $comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($comps) {
        //process competitions
        foreach ($comps as $c) {
            $start = safe_get($c, "created", "");
            $end = safe_get($c, "expires", "");
            $fp = (float)safe_get($c, "first_place", 0);
            $sp = (float)safe_get($c, "second_place", 0);
            $tp = (float)safe_get($c, "third_place", 0);
            $reward = (int)safe_get($c, "points", 0);
            $title = safe_get($c, "title", "N/A");
            $cid = safe_get($c, "id", -1);
            $query = "SELECT s.user_id, SUM(s.score) as total from tfp_scores s JOIN tfp_usercompetitions uc on s.user_id = uc.user_id WHERE s.created BETWEEN :start AND :end AND uc.competition_id = :cid group by user_id order by total desc limit 3";
            $stmt = $db->prepare($query);
            $r = $stmt->execute([":start" => $start, ":end" => $end, ":cid"=>$cid]);
            if ($r) {
                $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($players) {
                    if ($fp > 0 && count($players) >= 1) {
                        //generate reward
                        $fpw = (int)ceil($fp * $reward);
                        $winner = safe_get($players, 0, null);
                        if($winner){
                            $id = safe_get($players[0], "user_id", -1);
                            changePoints($id, $fpw, "First Place Competition: $title");
                        }
                        
                    }
                    if($sp > 0 && count($players) >= 2){
                        //gen reward
                        $spw = (int)ceil($sp * $reward);
                        $winner = safe_get($players, 1, null);
                        if($winner){
                            $id = safe_get($players[1], "user_id", -1);
                            changePoints($id, $spw, "Second Place Competition: $title");
                        }
                    }
                    if($tp > 0 && count($players) >= 3){
                        //gen reward
                        $tpw = (int)ceil($tp * $reward);
                        $winner = safe_get($players, 2, null);
                        if($winner){
                            $id = safe_get($players[2], "user_id", -1);
                            changePoints($id, $tpw, "Third Place Competition: $title");
                        }
                    }
                }
                
                $query = "UPDATE tfp_competitions set calced_winner = 1 where id = :cid";
                $stmt = $db->prepare($query);
                $stmt->execute([":cid"=>$cid]);
                $processed++;
            }
        }
    }
}
//TODO close all invalid competitions
$query = "UPDATE tfp_competitions set calced_winner = 1 WHERE expires <= current_timestamp() and calced_winner != 1 and participants < min_participants";
$db = getDB();
$stmt = $db->prepare($query);
$r = $stmt->execute();
$r = $stmt->rowCount();
$response["status"] = 200;
$response["message"] = "Processed $processed valid comps and closed $r invalid comps";
echo json_encode($response);
