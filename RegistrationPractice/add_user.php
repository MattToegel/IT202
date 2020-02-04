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
	

	 $stmt = $db->prepare("INSERT INTO `Users2`
                        (email) VALUES
                        (:email)");
    
        $params = array(":email"=> 'Bob@bob.com');
        $stmt->execute($params);
        echo "<pre>" . var_export(
                        $stmt->errorInfo(), true) . "</pre>";
	echo var_export($stmt->errorInfo(), true);
}
catch(Exception $e){
	echo $e->getMessage();
	exit("It didn't work");
}

?>
