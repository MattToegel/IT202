<?php
//these files in the API folder aren't expected to be fully user facing.
//A user shouldn't access these directly.
//we'll be using ajax to send/receive data here
require(__DIR__ . "/api_helpers.php"); //specific helpers just for API
if (!isAjax()) {
    die(header("Location: index.php"));
}
$response = ["message" => "An error occurred", "status" => 400]; //defined a response template with initially failure data
if (isset($_GET["id"])) {
    session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
    require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
    //Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
    //and the timing would be off since ajax doesn't trigger a page load

    if (is_logged_in()) {
        $user_id = get_user_id();
        $rock_id = (int)se($_GET, "id", 0, false);
        if ($rock_id > 0) {
            /*
            I'm going to insert the data from pending rocks into the rocks table if the below conditions are true:
            - rock belongs to user (owned_by)
            - no choice was made for the current batch
            - rock doesn't have a chosen date

            Note: This will seemingly give the Rock a new id since we're using Pending_Rock.id which isn't going to match with Rock.id
            */
            $query =
            "INSERT INTO Rocks (time_to_mine, potential_reward, percent_chance, owned_by, batches_id)
            SELECT time_to_mine, potential_reward, percent_chance, owned_by, batches_id 
            from Pending_Rocks pr JOIN Batches b ON pr.batches_id = b.id
             WHERE chosen_date is null AND pr.id = :rid AND owned_by = :uid AND b.made_choice = 0 LIMIT 1";
            $db = getDB();
            $stmt = $db->prepare($query);
            try {
                $result =  $stmt->execute([":rid" => $rock_id, ":uid" => $user_id]);
                if ($result) {
                    //TODO: see note about different db drivers:
                    //https://www.php.net/manual/en/pdostatement.rowcount.php
                    if ($stmt->rowCount() > 0) {
                        //update choice in Pending_Rocks and Batches
                        $query = "UPDATE Pending_Rocks SET chosen_date = CURRENT_TIMESTAMP where id = :rid";
                        $stmt = $db->prepare($query);
                        try {
                            $result =  $stmt->execute([":rid" => $rock_id]);
                        } catch (PDOException $e) {
                            error_log("Erorr updating date of pending rock: " . var_export($e->errorInfo, true));
                        }
                        $query = "UPDATE Batches SET made_choice = 1 WHERE id = (select batches_id from Pending_Rocks WHERE id = :rid LIMIT 1)";
                        $stmt = $db->prepare($query);
                        try {
                            $result =  $stmt->execute([":rid" => $rock_id]);
                        } catch (PDOException $e) {
                            error_log("Error made_choice of Batches: " . var_export($e->errorInfo, true));
                        }
                        $response["status"] = 200;
                        $response["message"] = "Successfully picked rock";
                    } else {
                        $response["message"] = "Error picking rock, please try again";
                    }
                }
            } catch (PDOException $e) {
                $err = var_export($e->errorInfo, true);
                error_log("Error inserting chosen rock: $err");
                $response["message"] = "Error inserting chosen rock: $err";
            }
        } else {
            $response["message"] = "Invalid rock chosen";
        }
    } else {
        $response["message"] = "User must be logged in";
    }
} else {
    $response["message"] = "Missing parameter id";
}
//with ajax you must be cautious with what you echo (or write) to the output buffer
//anything written will get sent as the response
//make sure you only echo just the encoded $response
echo json_encode($response);//<-- this is the "return" value to the request