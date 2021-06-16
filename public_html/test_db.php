<?php

require(__DIR__ . "/../lib/db.php");

$query = "SELECT 'test' from dual";
$db = getDB();//this is accessible via the db.php require above
$stmt = $db->query($query);//tells the DB to run the query defined above
$result = $stmt->fetch();
echo "<pre>" . var_export($result, true) . "</pre>";
?>
