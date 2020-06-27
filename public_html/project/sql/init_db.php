<?php
#turn error reporting on
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//pull in config.php so we can access the variables from it
require_once (__DIR__ . "/../includes/common.inc.php");
$conn_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
try{
    //name each sql file in a way that it'll sort correctly to run in the correct order
    //my samples prefix filenames with #_
    /***
     * Finds all .sql files in structure directory.
     * Generates an array keyed by filename and valued as raw SQL from file
     */
    foreach(glob(__DIR__ . "/structure/*.sql") as $filename){
        $sql[$filename] = file_get_contents($filename);
    }
    if(isset($sql) && $sql){
        /***
         * Sort the array so queries are executed in anticipated order.
         * Be careful with naming, 1-9 is fine, 1-10 has 10 run after #1 due to string sorting.
         */
        ksort($sql);
        echo "<br><pre>" . var_export($sql, true) . "</pre><br>";
        //connect to DB
        $db = getDB();
        foreach($sql as $key => $value){
            echo "<br>Running: " . $key;
            $stmt = $db->prepare($value);
            $result = $stmt->execute();
            $error = $stmt->errorInfo();
            if($error && $error[0] !== '00000'){
                echo "<br>Error:<pre>" . var_export($error,true) . "</pre><br>";
            }
            echo "<br>$key result: " . ($result>0?"Success":"Fail") . "<br>";
        }
    }
    else{
        echo "Didn't find any files, please check the directory/directory contents/permissions";
    }
    $db = null;
}
catch(Exception $e){
    echo $e->getMessage();
    exit("Something went wrong");
}
?>