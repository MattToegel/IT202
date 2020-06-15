<?php
require("common.inc.php");
$db = getDB();
//example usage, change/move as needed
$stmt = $db->prepare("SELECT * FROM Things");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo var_export($result, true);
?>