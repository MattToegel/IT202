<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("get_inventory received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$response = ["status" => 400, "message" => "Unhandled error"];
http_response_code(400);
if (is_logged_in()) {
    $db = getDB();
    /**item_id int,
    quantity int, */
    $stmt = $db->prepare("SELECT item_id, quantity, name, description FROM RM_Inventory inv JOIN RM_Items items on inv.item_id = items.id where inv.user_id = :uid and inv.quantity > 0");
    try {
        $stmt->execute([":uid" => get_user_id()]);
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response["message"] = "Retrieved inventory";
        if ($r) {
            $response["status"] = 200;
            $response["items"] = $r;
        } else {
            $response["status"] = 200;
            $response["items"] = [];
        }
    } catch (PDOException $e) {
        error_log("Error fetching inventory: " . var_export($e, true));
    }
} else {
    $response["status"] = 403;
    http_response_code(403);
    $response["message"] = "Must be logged in to fetch inventory";
}
error_log("Response: " . var_export($response, true));
echo json_encode($response);
