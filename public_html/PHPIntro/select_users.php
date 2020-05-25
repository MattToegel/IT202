<?php
require("config.php");

$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
try{
	$db = new PDO($connection_string, $dbuser, $dbpass);
	$stmt = $db->prepare("SELECT * from Users");
	$r = $stmt->execute();
	//$result = $stmt->fetch();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo var_export($stmt->errorInfo(), true);
	echo var_export($r, true);
	echo var_export($results, true);
}
catch (Exception $e){
	echo $e->getMessage();
}
?>