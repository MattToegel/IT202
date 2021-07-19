<?php
//these files in the API folder aren't expected to be fully user facing.
//A user shouldn't access these directly.
//we'll be using ajax to send/receive data here
require(__DIR__ . "/api_helpers.php"); //specific helpers just for API
if (!isAjax()) {
    die(header("Location: index.php"));
}
$response = ["message" => "An error occurred", "status" => 400]; //defined a response template with initially failure data
//check your data and try to fail early to reduce wasted resources
if (isset($_POST["rock_id"]) && isset($_POST["tool_id"])) {
    session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
    require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
    //Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
    //and the timing would be off since ajax doesn't trigger a page load

    if (is_logged_in()) {
        $user = get_user_id();
        $rock_id = (int)se($_POST, "rock_id", 0, false);
        $tool_id = (int)se($_POST, "tool_id", 0, false);
        error_log("user $user, rock $rock_id, tool $tool_id");
        if ($rock_id > 0 && $user > 0 && $tool_id > 0) {
            //note we're joining on a column that's not a common FK between these tables
            //in this case, I want the data from both where the user_id is the same
            $query = "SELECT time_to_mine, IF(opens_date is null, -1,given_reward) as `given_reward`, is_mining, quantity, `mod` from Rocks r JOIN Inventory inv on inv.user_id = r.owned_by
            WHERE r.owned_by = :uid AND r.id = :rid AND inv.id = :tid"; //inv.id works here, we're actually passing the id of the inventory not the item_id
            $db = getDB();
            $stmt = $db->prepare($query);
            $data = [];
            try {
                $stmt->execute([":uid" => $user, ":rid" => $rock_id, ":tid" => $tool_id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC); //it really should be 1 record given the data
                error_log("data: " . var_export($data, true));
            } catch (PDOException $e) {
                error_log("Error fetching rock/item combo: " . var_export($e->errorInfo, true));
            }
            if ($data && count($data) > 0) {
                $quantity = (int)se($data, "quantity", 0, false);
                $isValid = true;
                $response["message"] = "Criteria error:";
                if ($quantity <= 0) {
                    $response["message"] .= " <br>You don't have any more tools of the selected type.";
                    $isValid = false;
                }
                $given_reward = ((int)se($data, "given_reward", 0, false)) > -1;
                if ($given_reward) {
                    $response["message"] .= " <br>This rock has already been mined.";
                    $isValid = false;
                }
                $is_mining = ((int)se($data, "is_mining", 0, false)) > 0;
                if ($is_mining) {
                    $response["message"] .= " <br>This rock is already being mined.";
                    $isValid = false;
                }
                if ($isValid) {
                    //remove tool
                    //ensuring the item is indeed owned by the user
                    $query = "UPDATE Inventory set quantity = quantity - 1 WHERE user_id = :uid AND id = :tid";

                    $stmt = $db->prepare($query);
                    try {
                        $stmt->execute([":uid" => $user, ":tid" => $tool_id]);
                    } catch (PDOException $e) {
                        error_log("Error removing item from user[$user] inventory with item id [$tool_id]: " . var_export($e->errorInfo, true));
                        $isValid = false;
                    }
                    if ($isValid) {
                        //start mining
                        $days = (float)se($data, "time_to_mine", 999, false);
                        //convert to minutes
                        $minutes = $days * 24 * 60;
                        error_log("Original minutes: $minutes");
                        $mod = (float)se($data, "mod", 0, false);
                        if ($mod != 0) { //let mod be negative for a "debuff" or positive for a "buff"
                            $minutes -= $minutes * $mod;
                        }
                        error_log("Mod $mod result minutes $minutes");
                        //use minutes for the datetime (fractional days calculated weird)
                        $opens_date = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . " +$minutes minute"));
                        //TODO probably should validate the new date
                        $query = "UPDATE Rocks set opens_date = :date, is_mining = 1, mod_time_to_mine = :mod WHERE id = :rid";
                        $params = [":date" => $opens_date, ":mod" => ($minutes /*/ 24 / 60*/), ":rid" => $rock_id];
                        $stmt = $db->prepare($query);
                        try {
                            $stmt->execute($params);
                            $response["status"] = 200;
                            $response["message"] = "You started mining rock #$rock_id! Good Luck!";
                        } catch (PDOException $e) {
                            error_log("Error updating rock to mine: " . var_export($e->errorInfo, true));
                            $response["message"] = "There was an error mining the rock, please try again";
                        }
                    }
                }
            } else {
                $response["message"] = "There was a problem validating the chosen rock and item";
            }
        } else {
            $response = "Invalid mine attempt";
        }
    } else {
        $response["message"] = "User must be logged in";
    }
} else {
    $response["message"] = "Missing expected field 'rock_id' or 'tool_id'";
}
//with ajax you must be cautious with what you echo (or write) to the output buffer
//anything written will get sent as the response
//make sure you only echo just the encoded $response
echo json_encode($response);//<-- this is the "return" value to the request