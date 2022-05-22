<?php
//for this we'll turn on error output so we can try to see any problems on the screen
//this will be active for any script that includes/requires this one
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function getDB(){
    global $db;
    //this function returns an existing connection or creates a new one if needed
    //and assigns it to the $db variable
    if(!isset($db)) {
        try{
            //__DIR__ helps get the correct path regardless of where the file is being called from
            //it gets the absolute path to this file, then we append the relative url (so up a directory and inside lib)
            require_once(__DIR__. "/config.php");//pull in our credentials
            //use the variables from config to populate our connection
            $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
            //using the PDO connector create a new connect to the DB
            //if no error occurs we're connected
            $db = new PDO($connection_string, $dbuser, $dbpass);
	    //the default fetch mode is FETCH_BOTH which returns the data as both an indexed array and associative array
	    //we'll override the default here so it's always fetched as an associative array
 	    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
   	catch(Exception $e){
            error_log("getDB() error: " . var_export($e, true));
            $db = null;
        }
    }
    return $db;
}
?>
