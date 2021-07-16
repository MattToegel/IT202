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
            //07/12/2021 added iname for internal name
            $query = "SELECT iname, name,cost,description from Items where id = :pid and stock > 0";
            $db = getDB();
            $stmt = $db->prepare($query);
            try {
                $stmt->execute([":pid" => $product_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $cost = (int)se($result, "cost", 99999, false);
                    $name = se($result, "name", "N/A", false);
                    $iname = se($result, "iname", "", false); //internal name, better for working in code
                    $description = se($result, "description", "", false);//used below to extract %
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
                            switch ($iname) {
                                case "quarry_voucher":
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
                            if (str_contains($iname, "pickaxe")) {
                                //insert or update our item
                                $query = "INSERT INTO Inventory (item_id, user_id, quantity, `mod`) VALUES (:i, :u, 1, :m) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
                                $stmt = $db->prepare($query);
                                try {
                                    $mod = 0;
                                    //used to extract a % from the description to later utilize a modifier (i.e., pickaxes)
                                    //really this should be stored better in a different column, but this is a lazy implementation based on existing data structure
                                    if (str_contains($description, "%")) {
                                        error_log("Description: $description");
                                        $d1 = explode("%", $description)[0]; //left of %
                                        error_log($d1);
                                        $d2 = explode(" ", $d1);
                                        $d2 = $d2[count($d2) - 1]; //right of space
                                        error_log($d2);
                                        $mod = floatval($d2);
                                        error_log($mod);
                                        $mod /= 100.0;
                                        error_log($mod);
                                    }
                                    $stmt->execute([":i" => $product_id, ":u" => get_user_id(), ":m" => $mod]);
                                } catch (PDOException $e) {
                                    error_log("Error adding pickaxe to inventory: " . var_export($e->errorInfo, true));
                                }
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
                    $response["message"] = "Item not found or out of stock";
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