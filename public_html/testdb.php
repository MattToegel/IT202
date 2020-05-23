<?php
require("config.php");

$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
try{
$db = new PDO($connection_string, $dbuser, $dbpass);
}
catch (Exception $e){
	echo $e->getMessage();
}
?>