<?php

session_start();
require(__DIR__ . "/../../../lib/functions.php");
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    die(header("Location: " . get_url("admin/list_stocks.php")));
}

$db = getDB();
$query = "DELETE FROM `IT202-S24-Stocks` WHERE id = :id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    flash("Deleted record with id $id", "success");
} catch (Exception $e) {
    error_log("Error deleting stock $id" . var_export($e, true));
    flash("Error deleting record", "danger");
}
die(header("Location: " . get_url("admin/list_stocks.php")));
