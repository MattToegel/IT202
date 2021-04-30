<?php
$response = array("status" => 400, "message" => "Error saving score");
if (isset($_POST["compId"])) {
    require(__DIR__ . "/../lib/helpers.php");
    if (is_logged_in(false)) {
        $user_id = get_user_id();
        $compId = safe_get($_POST, "compId", -1);
        if ($compId > -1) {
            $db = getDB();
            $query = "SELECT IFNULL(entry_fee, -1) as `entryFee` from tfp_competitions where expires > curdate() and calced_winner != 1 and id = :cid";
            $stmt = $db->prepare($query);
            $r = $stmt->execute([":cid" => $compId]);
            if ($r) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $fee = (int)safe_get($result, "entryFee", -1);
                    if ($fee < 0) {
                        $response["message"] = "Invalid entry fee";
                    } else {
                        $balance = get_points_balance();
                        if ($fee > $balance) {
                            $response["message"] = "You can't afford to join this yet";
                        } else {
                            $query = "INSERT INTO tfp_usercompetitions (user_id, competition_id) VALUES (:uid, :cid)";

                            $stmt = $db->prepare($query);
                            $r = $stmt->execute([":uid" => $user_id, ":cid" => $compId]);
                            if ($r) {
                                changePoints($user_id, -$fee, "Joined competition: $compId");

                                $query = "UPDATE tfp_competitions set participants = (select IFNULL(count(user_id),0) from tfp_usercompetitions uc where uc.competition_id = :cid) where id = :cid";
                                $stmt = $db->prepare($query);
                                $stmt->execute([":cid"=>$compId]);
                                $response["status"] = 200;
                                $response["message"] = "Joined successfully";
                            } else {
                                $e = $stmt->errorInfo();
                                if ($e[0] == "23000") {
                                    $response["message"] = "Already registered for competition";
                                } else {
                                    $response["message"] = "Error joining competition: " . var_export($e, true);
                                }
                            }
                        }
                    }
                } else {
                    $response["message"] = "Error looking up competition";
                }
            } else {
                $response["message"] = "Error looking up competition: " . var_export($stmt->errorInfo(), true);
            }
        } else {
            $response["message"] = "Invalid competition";
        }
    } else {
        $response["message"] = "User must be logged in";
    }
}
echo json_encode($response);
