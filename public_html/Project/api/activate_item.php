<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("activate item received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
//handle the potentially incoming post request
$item_id = (int)se($_POST, "item_id", null, false);
$quantity = (int)se($_POST, "quantity", 1, false); //not used at the moment
$response = ["status" => 400, "message" => "Invalid data"];
http_response_code(400);
if (isset($item_id) && $quantity > 0) {
    if (is_logged_in()) {
        $db = getDB();
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO RM_Active_Items (item_id, quantity, user_id) VALUES(:iid, :q, :uid) 
        ON DUPLICATE KEY UPDATE quantity = quantity + :q");
        $stmt->bindValue(":iid", $item_id, PDO::PARAM_INT);
        $stmt->bindValue(":q", 1/*hard coding 1 for now*/, PDO::PARAM_INT);
        $stmt->bindValue(":uid", get_user_id(), PDO::PARAM_INT);
        try {
            $stmt->execute();
            $response["status"] = 418; // :)
            $response["message"] = "Item activated";
            http_response_code(200);

            $stmt = $db->prepare("UPDATE RM_Inventory SET quantity = quantity - :q WHERE user_id = :uid AND item_id = :iid");
            try {
                $stmt->bindValue(":iid", $item_id, PDO::PARAM_INT);
                $stmt->bindValue(":q", 1/*hard coding 1 for now*/, PDO::PARAM_INT);
                $stmt->bindValue(":uid", get_user_id(), PDO::PARAM_INT);
                $stmt->execute();
                $db->commit();
                //map useage
                activate_item($item_id);
            } catch (PDOException $e) {
                error_log("Deduct active item error: " . var_export($e, true));
                $response["message"] = "Error activating item";
                $db->rollback();
            }
        } catch (PDOException $e) {
            error_log("Activate item error: " . var_export($e, true));
            $response["message"] = "Error activating item";
            $db->rollback();
        }
    } else {
        http_response_code(403);
        $response["status"] = 403;
        $response["message"] = "Must be logged in to add to cart";
    }
}
echo json_encode($response);
