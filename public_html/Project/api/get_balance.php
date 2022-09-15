<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("get_balance received data: " . var_export($_REQUEST, true));
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$response["balance"] = get_account_balance();
echo json_encode($response);
