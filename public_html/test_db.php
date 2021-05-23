<?php

require(__DIR__ . "/../lib/db.php");

$query = "SELECT 'test' from dual";
$db = getDB();//this is accessible via the db.php require above
$row = $db->query($query, PDO::FETCH_ASSOC);//tells the DB to run the query defined above
echo "<pre>" . var_export($row, true) . "</pre>";
?>
