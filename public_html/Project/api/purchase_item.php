<?php
//these files in the API folder aren't expected to be fully user facing.
//A user shouldn't access these directly.
//we'll be using ajax to send/receive data here
require(__DIR__ . "/api_helpers.php"); //specific helpers just for API
if (!isAjax()) {
    die(header("Location: index.php"));
}
$response = ["message" => "An error occurred", "status" => 400]; //defined a response template with initially failure data
if (isset($_POST["product_id"])) {
    $product_id = (int)$_POST["product_id"];
    if ($product_id > -1) {
        session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
        require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
        //Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
        //and the timing would be off since ajax doesn't trigger a page load

        if (is_logged_in()) {
            $balance = get_account_balance();
            //lookup item by id, don't trust anything from the client
            $query = "SELECT name,cost from Items where id = :pid and stock > 0";
            $db = getDB();
            $stmt = $db->prepare($query);
            try {
                $stmt->execute([":pid" => $product_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $cost = (int)se($result, "cost", 99999, false);
                    $name = se($result, "name", "N/A", false);
                    if ($cost <= $balance) {
                        //deduct cost from user balance
                        change_points($cost, "purchase", get_user_account_id(), -1, "Purchased: " . $name);
                        $hadError = false; //used to prevent error message from being overwritten

                        //update quantity post-purchase
                        $query = "UPDATE Items set stock = stock-1 WHERE id = :pid";
                        $stmt = $db->prepare($query);
                        try {
                            $stmt->execute([":pid" => $product_id]);
                        } catch (PDOException $e) {
                            $err = var_export($e->errorInfo, true);
                            error_log("Error updating product quantity: $err");
                            $response["message"] = "Error updating product quantity: $err";
                            $hadError = true;
                        }
                        //string matching like this isn't the best way to do it, but I rather not potentially hardcode id's that could change
                        //TODO move special purchase logic elsewhere
                        if (!$hadError) {
                            switch ($name) {
                                case "Quarry Voucher":
                                    $query = "UPDATE Accounts set quarry_vouchers = quarry_vouchers + 1 WHERE id = :aid";
                                    $stmt = $db->prepare($query);
                                    try {
                                        $r = $stmt->execute([":aid" => get_user_account_id()]);
                                    } catch (PDOException $e) {
                                        $response["message"] = "Unknown error updating account: " . var_export($e->errorInfo, true);
                                        $hadError = true;
                                    }
                                    get_or_create_account(); //pull latest account info into session
                                    break;
                            }
                        }
                        //TODO update success message handling, if the update in line 35 fails it'll still show success
                        if (!$hadError) {
                            $response["status"] = 200;
                            $response["message"] = "Thank you for your purchase of $name";
                        }
                    } else {
                        $response["message"] = "You can't afford this";
                    }
                } else {
                    $response["message"] = "Product not found or out of stock";
                }
            } catch (PDOException $e) {
                $response["message"] = "Unknown error fetching item: " . var_export($e->errorInfo, true);
            }
        } else {
            $response["message"] = "User must be logged in";
        }
    } else {
        $response["message"] = "Invalid product id";
    }
} else {
    $response["message"] = "Missing product id";
}
//with ajax you must be cautious with what you echo (or write) to the output buffer
//anything written will get sent as the response
//make sure you only echo just the encoded $response
echo json_encode($response);//<-- this is the "return" value to the request