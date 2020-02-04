<?php
//this is check_db.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("config.php");
echo "DBUser: " .  $dbuser;
echo "\n\r";

$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";

try{
	$db = new PDO($connection_string, $dbuser, $dbpass);
	echo "Should have connected";
	

	 $stmt = $db->prepare("SELECT * from `Users2` where id = :id");
    
        $params = array(":id"=> '1');
        $stmt->execute($params);
	$results = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>" . var_export(
                        $stmt->errorInfo(), true) . "</pre>";
	echo var_export($results, true);
}
catch(Exception $e){
	echo $e->getMessage();
	exit("It didn't work");
}

?>
