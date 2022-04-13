<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("get_cart received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$user_id = get_user_id();

$response = ["status" => 400, "message" => "Unhandled error"];
http_response_code(400);
if ($user_id > 0) {
    $db = getDB();
    $stmt = $db->prepare("SELECT name, c.id as line_id, item_id, quantity, cost, (cost*quantity) as subtotal FROM RM_Cart c JOIN RM_Items i on c.item_id = i.id WHERE c.user_id = :uid");
    try {
        $stmt->execute([":uid" => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        if ($results) {
            $response["status"] = 200;
            $response["message"] = "Retrieved cart";
            $response["data"] = $results;
        } else {
            $response["status"] = 200;
            $response["message"] = "No items in cart";
            $response["data"] = [];
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
