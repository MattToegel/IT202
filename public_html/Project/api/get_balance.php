<?php
require_once(__DIR__ . "/../../../lib/functions.php");
session_start();
echo json_encode(["balance" => get_account_balance()]);
