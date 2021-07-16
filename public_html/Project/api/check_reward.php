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
if (isset($_POST["rock_id"])) {
    session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
    require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
    //Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
    //and the timing would be off since ajax doesn't trigger a page load

    if (is_logged_in()) {
        $user = get_user_id();
        $rock_id = (int)se($_POST, "rock_id", 0, false);
        error_log("user $user, rock $rock_id");
        if ($rock_id > 0 && $user > 0) {

            $query = "SELECT time_to_mine,percent_chance,potential_reward, IF(opened_date is null, -1,given_reward) as `given_reward`, is_mining, IF(opens_date <= current_timestamp, 1,0) as ready from Rocks r
            WHERE r.owned_by = :uid AND r.id = :rid";
            $db = getDB();
            $stmt = $db->prepare($query);
            $data = [];
            try {
                $stmt->execute([":uid" => $user, ":rid" => $rock_id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC); //it really should be 1 record given the data
                error_log("data: " . var_export($data, true));
            } catch (PDOException $e) {
                error_log("Error fetching rock/item combo: " . var_export($e->errorInfo, true));
            }
            if ($data && count($data) > 0) {
                $isReady = ((int)se($data, "ready", 0, false)) > 0;
                $isValid = true;
                $response["message"] = "Criteria error:";
                //make sure the rock is indeed ready to mine
                if (!$isReady) {
                    $response["message"] .= " <br>The mining time has not yet expired for this rock.";
                    $isValid = false;
                }
                //make sure the rock hasn't rewarded the player yet
                $given_reward = ((int)se($data, "given_reward", 0, false)) > -1;
                error_log($given_reward . " " . ((int)se($data, "given_reward", 0, false)));
                if ($given_reward) {
                    $response["message"] .= " <br>This rock has already been mined.";
                    $isValid = false;
                }
                //make sure it's being mined
                $is_mining = ((int)se($data, "is_mining", 0, false)) > 0;
                if (!$is_mining) {
                    $response["message"] .= " <br>This rock is not currently being mined.";
                    $isValid = false;
                }
                //make sure the percentage is valid 0-100%
                $percent_chance = (float)se($data, "percent_chance", 0, false);
                if ($percent_chance <= 0 || $percent_chance > 1.0) {
                    $response["message"] .= "<br> Invalid reward percentage";
                    $isValid = false;
                }
                if ($isValid) {
                    $chance = (float)(((float)random_int(0, 100)) / 100.0);
                    $potential_reward = (int)se($data, "potential_reward", 0, false);
                    $reward = 0;
                    error_log("Rock $rock_id Chance: $percent_chance Random: $chance");
                    //check if the reward will be given
                    if ($chance <= $percent_chance) {
                        //win
                        $reward = $potential_reward;
                        $response["message"] = "Congrats! You earned $reward point(s)!";
                        $response["status"] = 200;
                    } else {
                        //lose
                        $response["message"] = "Sorry, you didn't earn anything from this rock. Better luck next time";
                        $response["status"] = 200;
                    }
                    //update the rock to set the opened date and given reward (so it can't be opened again for duplicate reward)
                    $query = "UPDATE Rocks set opened_date = current_timestamp, given_reward = :reward WHERE id = :rid";
                    $stmt = $db->prepare($query);
                    try {
                        $stmt->execute([":reward" => $reward, ":rid" => $rock_id]);
                    } catch (PDOException $e) {
                        error_log("Error updating rock opened date: " . var_export($e->errorInfo, true));
                    }
                    //I only record transactions that are not 0, the function checks this, but it doesn't hurt to validate here as well
                    if ($reward > 0) {
                        change_points($reward, "mining", -1, get_user_account_id(), "Rock #$rock_id gave you $reward points");
                        update_score();
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