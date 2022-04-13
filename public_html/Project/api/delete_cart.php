<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("add_to_cart received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$user_id = get_user_id();

$response = ["status" => 400, "message" => "Unhandled error"];
http_response_code(400);
$line_id = se($_REQUEST, "line_id", 0, false);
if ($user_id > 0 && $line_id > 0) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM RM_Cart where id = :id and :uid");
    try {
        //added user_id to ensure the user can only delete their own items
        $stmt->execute([":id" => $line_id, ":uid" => $user_id]);
        $response["status"] = 200;
        $response["message"] = "Deleted line item";
        http_response_code(200);
    } catch (PDOException $e) {
        error_log("Error deleting line item: " . var_export($e, true));
        $response["message"] = "Error deleting item";
    }
}
echo json_encode($response);
