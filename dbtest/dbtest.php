<?php
//include our db file
require("db.php");
//use the function that we just pulled in
$db = getDB();
//make sure it's set
if(isset($db)){
        //we'll fetch our sql file from earlier
        $query = file_get_contents("create_table_users.sql");
        //prepares the query safely to reduce SQL injection (discussed later)
        $stmt = $db->prepare($query);
        //runs the query
        $stmt->execute();
        //checks if we have an error info populated
        //by default it's always populated so success is if index 0 is 5 zeroes
        $e = $stmt->errorInfo();
        if($e[0] != '00000'){
                echo "Query error: " . var_export($e, true);       
        }
        else{
                echo "table created successfully";
        }
        
}
else{
        echo "there may be a problem with our connection details";
}
?>
