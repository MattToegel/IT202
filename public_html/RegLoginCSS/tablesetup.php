<?php
require("config.php");

$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
try{
	$db = new PDO($connection_string, $dbuser, $dbpass);
	$stmt = $db->prepare("CREATE TABLE `Users` (
				`id` int auto_increment not null,
				`email` varchar(100) not null unique,
				`first_name` varchar(100),
				`last_name` varchar(100),
				`password` varchar(60),
				`created` timestamp default current_timestamp,
				`modified` timestamp default current_timestamp on update current_timestamp,
				PRIMARY KEY (`id`)
				) CHARACTER SET utf8 COLLATE utf8_general_ci");
	$r = $stmt->execute();
	echo var_export($stmt->errorInfo(), true);
	echo var_export($r, true);
}
catch (Exception $e){
	echo $e->getMessage();
}
?>