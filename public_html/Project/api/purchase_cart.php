<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("purchase_cart received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$user_id = get_user_id();

$response = ["status" => 400, "message" => "There was a problem completing your purchase"];
http_response_code(400);
if ($user_id > 0) {
    $db = getDB();
    $stmt = $db->prepare("SELECT name, c.id as line_id, item_id, quantity, cost, (cost*quantity) as subtotal FROM RM_Cart c JOIN RM_Items i on c.item_id = i.id WHERE c.user_id = :uid");
    try {
        $stmt->execute([":uid" => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $balance = get_account_balance();
        $total_cost = 0;
        foreach ($results as $row) {
            $total_cost += (int)se($row, "subtotal", 0, false);
        }
        if ($balance >= $total_cost) {
            //can purchase
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT max(order_id) as order_id FROM RM_OrderHistory");
            $next_order_id = 0;
            //get next order id
            try {
                $stmt->execute();
                $r = $stmt->fetch(PDO::FETCH_ASSOC);
                $next_order_id = (int)se($r, "order_id", 0, false);
                $next_order_id++;
            } catch (PDOException $e) {
                error_log("Error fetching order_id: " . var_export($e));
                $db->rollback();
            }
            //deduct product stock (used to determine if out of stock as it'll fail with a constraint violation)
            if ($next_order_id > 0) {
                $stmt = $db->prepare("UPDATE RM_Items 
                set stock = stock - (select IFNULL(quantity, 0) FROM RM_Cart WHERE item_id = RM_Items.id and user_id = :uid) 
                WHERE id in (SELECT item_id from RM_Cart where user_id = :uid)");
                try {
                    $stmt->execute([":uid" => $user_id]);
                } catch (PDOException $e) {
                    error_log("Update stock error: " . var_export($e, true));
                    $response["message"] = "At least one of your items is low on stock and is unable to be purchased";
                    $db->rollback();
                    $next_order_id = 0; //using as a controller
                }
            }
            //map cart to order history
            if ($next_order_id > 0) {
                $stmt = $db->prepare("INSERT INTO RM_OrderHistory (item_id, user_id, quantity, cost, order_id) 
                SELECT item_id, user_id, RM_Cart.quantity, cost, :order_id FROM RM_Cart JOIN RM_Items on RM_Cart.item_id = RM_Items.id
                 WHERE user_id = :uid");
                try {
                    $stmt->execute([":uid" => $user_id, ":order_id" => $next_order_id]);
                } catch (PDOException $e) {
                    error_log("Error mapping cart data to order history: " . var_export($e, true));
                    $db->rollback();
                    $next_order_id = 0; //using as a controller
                }
            }
            //update inventory
            if ($next_order_id > 0) {
                $stmt = $db->prepare("INSERT INTO RM_Inventory (item_id, quantity, user_id)
                SELECT item_id, quantity, user_id FROM RM_Cart WHERE user_id = :uid
                ON DUPLICATE KEY UPDATE RM_Inventory.quantity = RM_Inventory.quantity + RM_Cart.quantity");
                try {
                    $stmt->execute([":uid" => $user_id]);
                } catch (PDOException $e) {
                    error_log("Error updating user's inventory: " . var_export($e, true));
                    $db->rollback();
                    $next_order_id = 0; // using as a controller
                }
            }
            //clear the user's cart now that the process is done
            if ($next_order_id > 0) {
                $stmt =  $db->prepare("DELETE from RM_Cart where user_id = :uid");
                try {
                    $stmt->execute([":uid" => $user_id]);
                } catch (PDOException $e) {
                    error_log("Error deleting cart: " . var_export($e, true));
                    $db->rollback();
                    $next_order_id = 0; // using as a controller
                }
            }
            if ($next_order_id) {

                //deduct points
                give_gems($total_cost, "purchase", get_user_account_id(), -1, "Spent $total_cost for items in the shop");
                $db->commit(); //confirm pending changes
                $response["status"] = 200;
                http_response_code(200);
                $response["message"] = "Purchase complete";
            } else {
                $response["status"] = 200;
                http_response_code(200);
            }
        } else {
            $response["status"] = 402;
            http_response_code(200);
            $response["message"] = "You can't afford to purchase your cart";
        }
    } catch (PDOException $e) {
        error_log("Error fetching cart" . var_export($e, true));
    }
} else {
    $response["status"] = 403;
    $response["message"] = "You must be logged in to fetch your cart";
    http_response_code(403);
}
echo json_encode($response);
