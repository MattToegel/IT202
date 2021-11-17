<?php
//remember, API endpoints should only echo/output precisely what you want returned
//any other unexpected characters can break the handling of the response
$response = ["message" => "There was a problem completing your purchase"];
http_response_code(400);
error_log("req: " . var_export($_POST, true));
if (isset($_POST["item_id"]) && isset($_POST["quantity"]) && isset($_POST["cost"])) {
    require_once(__DIR__ . "/../../../lib/functions.php");
    session_start();
    $user_id = get_user_id();
    $balance = get_account_balance();
    $item_id = (int)se($_POST, "item_id", 0, false);
    $quantity = (int)se($_POST, "quantity", 0, false);
    $cost = (int)se($_POST, "cost", 0, false);
    $isValid = true;
    $errors = [];
    if ($user_id <= 0) {
        //invald user
        array_push($errors, "Invalid user");
        $isValid = false;
    }
    if ($balance <= 0 && $cost > 0) {
        //not enough funds
        array_push($errors, "Invalid balance or cost");
        $isValid = false;
    }
    //I'll have predefined items loaded in at negative values
    //so I don't need/want this check
    /*if ($item_id <= 0) {
        //invalid item
        array_push($errors, "Invalid item");
        $isValid = false;
    }*/
    if ($quantity <= 0) {
        //invalid quantity
        array_push($errors, "Invalid quantity");
        $isValid = false;
    }
    if ($cost > $balance) {
        //can't afford
        array_push($errors, "Insufficient funds");
        $isValid = false;
    }
    //get true price from DB
    $db = getDB();
    $stmt = $db->prepare("SELECT name,cost FROM BGD_Items where id = :id");
    $name = "";
    try {
        $stmt->execute([":id" => $item_id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $cost = (int)se($r, "cost", 0, false);
            $name = se($r, "name", "", false);
        }
    } catch (PDOException $e) {
        error_log("Error getting cost of $item_id: " . var_export($e->errorInfo, true));
        $isValid = false;
    }
    if ($isValid) {
        if (change_bills($cost * $quantity, "purchase", get_user_account_id(), -1, "Purchased $quantity $name")) {
            record_purchase($item_id, $user_id, $quantity);
            add_item($item_id, $user_id, $quantity);
            http_response_code(200);
            $response["message"] = "Purchased $quantity of $name";
            //success
        } else {
            error_log("Problem creating transaction");
        }
    } else {
        $response["message"] = join("<br>", $errors);
    }
}
echo json_encode($response);
