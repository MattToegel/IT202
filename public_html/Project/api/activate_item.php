<?php
require_once(__DIR__ . "/../../../lib/functions.php");
session_start();
$response = ["message" => "There was a problem completing your purchase"];
http_response_code(400);
if (isset($_POST["item_id"])) {
    $item_id = se($_POST, "item_id", 0, false);
    $db = getDB();
    //deduct item
    $stmt = $db->prepare("UPDATE BGD_Inventory set quantity = quantity - 1 WHERE item_id = :id");
    $usedItem = false;
    try {
        $stmt->execute([":id" => $item_id]);
        //TODO check if "check" constraint failed (quantity < 0)
        //TODO check affected rows (0 means they didn't own the item)
        $usedItem = true;
    } catch (PDOException $e) {
        error_log("Use Item Error: " . var_export($e->errorInfo, true));
    }
    if ($usedItem) {
        $item = [];
        $stmt = $db->prepare("SELECT uses from BGD_Items WHERE id = :id");
        try {
            $stmt->execute([":id" => $item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch Item Error: " . var_export($e->errorInfo, true));
        }
        if ($item) {
            $stmt = $db->prepare("INSERT INTO BGD_ActiveEffects (item_id, user_id, uses) VALUES (:iid, :uid, :u) ON DUPLICATE KEY UPDATE uses = uses + :u)");
            try {
                $stmt->bindValue(":u", se($item, "uses", 1, false), PDO::PARAM_INT);
                $stmt->bindValue(":iid", $item_id, PDO::PARAM_INT);
                $stmt->bindValue(":uid", $user_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                error_log("Activate Item Error: " . var_export($e->errorInfo, true));
            }
        }
    }
}
