<?php
//these files in the API folder aren't expected to be fully user facing.
//A user shouldn't access these directly.
//we'll be using ajax to send/receive data here
require(__DIR__ . "/api_helpers.php"); //specific helpers just for API
if (!isAjax()) {
    die(header("Location: index.php"));
}
$response = ["message" => "An error occurred", "status" => 400]; //defined a response template with initially failure data

session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
//Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
//and the timing would be off since ajax doesn't trigger a page load

//Note: Below is an example of how functions can be utilized to help improve the readability of the code
function lookup_vouchers() {
    global $db;
    global $response;
    $query = "SELECT IFNULL(quarry_vouchers, 0) as vouchers FROM Accounts WHERE id = :aid";
    $stmt = $db->prepare($query);
    $vouchers = 0;
    try {
        $stmt->execute([":aid" => get_user_account_id()]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && isset($r["vouchers"])) {
            $vouchers = (int)se($r, "vouchers", 0, false);
        }
    } catch (PDOException $e) {
        $response["message"] = "Error looking up vouchers: " . var_export($e->errorInfo, true);
    }
    return $vouchers;
}
function check_prospect_status() {
    global $db;
    global $response;
    $query = "SELECT count(1) as prospect FROM Batches WHERE user_id = :uid AND made_choice = 0";
    $stmt = $db->prepare($query);
    $count = 0;
    try {
        $stmt->execute([":aid" => get_user_account_id()]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $count = (int)se($r, "prospect", 0, false);
        }
    } catch (PDOException $e) {
        $response["message"] = "Error looking up batches: " . var_export($e->errorInfo, true);
    }
    return $count;
}
function remove_voucher() {
    global $db;
    global $response;
    $query = "UPDATE Accounts set quarry_vouchers = quarry_vouchers - 1 WHERE id = :aid";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":aid" => get_user_account_id()]);
        get_or_create_account(); //refreshes account data in session
        return true;
    } catch (PDOException $e) {
        $response["message"] = "Error updating voucher quantity";
    }
    return false;
}
function generate_prospect_options() {
    global $db;
    global $response;

    $query = "INSERT INTO Batches (user_id) values (:uid)";
    $stmt = $db->prepare($query);
    $batch_id = -1;
    try {
        $stmt->execute([":uid" => get_user_id()]);
        $batch_id = $db->lastInsertId();
    } catch (PDOException $e) {
        $response["message"] = "Error creating batch: " . var_export($e->errorInfo, true);
    }
    if ($batch_id > 0) {
        //generate 3 rocks
        $rocks = [];
        for ($i = 0; $i < 3; $i++) {
            //random reward between 1 - 100
            $potential_reward = random_int(1, 100);
            $chance = (float)$potential_reward;
            $chance /= (float)100.0;
            //random num of days between 1 and reward percent of 30 (1 month)
            $max = max(3, (30 * $chance)); //min 3
            $time_to_mine = random_int(1, $max);
            //rocks have a 10-90% chance range
            $percent_chance = random_int(10, 90) / 100.0;
            $rocks[":r$i"] = $potential_reward;
            $rocks[":t$i"] = $time_to_mine;
            $rocks[":c$i"] = $percent_chance;
        }
        error_log("Generated rocks: " . var_export($rocks, true));
        $query =
        "INSERT INTO Pending_Rocks(time_to_mine, potential_reward, percent_chance, owned_by, batches_id) VALUES
        (:t0, :r0, :c0, :uid, :bid),
        (:t1, :r1, :c1, :uid, :bid),
        (:t2, :r2, :c2, :uid, :bid)";
        $stmt = $db->prepare($query);
        try {
            $rocks[":uid"] = get_user_id();
            $rocks[":bid"] = $batch_id;
            $stmt->execute($rocks);
            return true;
        } catch (PDOException $e) {
            $err = var_export($e->errorInfo, true);
            error_log("Error creating pending rocks: " . $err);
            $response["message"] = "Error creating pending rocks: $e";
        }
    }
    return false;
}
if (is_logged_in()) {
    $vouchers = get_vouchers();
    if ($vouchers > 0) { //early check before we potentially waste a db call
        //although this should be accurate, lets check the source of truth
        $db = getDB();
        $vouchers = lookup_vouchers();
        if ($vouchers > 0) {
            $prospect = check_prospect_status();
            if ($prospect === 0) {
                $deducted = remove_voucher();
                if ($deducted) {
                    $success = generate_prospect_options();
                    if ($success) {
                        $response["status"] = 200;
                        $response["message"] = "Retrieved batch of rocks to prospect";
                    }
                }
            } else {
                $response["message"] = "Already prospecting; can only use 1 voucher at a time";
            }
        } else {
            $response["message"] = "Insufficient vouchers available";
        }
    } else {
        $response["message"] = "Insufficient vouchers available";
    }
} else {
    $response["message"] = "User must be logged in";
}
//with ajax you must be cautious with what you echo (or write) to the output buffer
//anything written will get sent as the response
//make sure you only echo just the encoded $response
echo json_encode($response);//<-- this is the "return" value to the request