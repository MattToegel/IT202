<?php
function get($arr, $key){
    if(isset($arr[$key])){
        return $arr[$key];
    }
    return "";
}
function getDB(){
    global $db;
    if(!isset($db)) {
        require_once("config.php");
        $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
        $db = new PDO($connection_string, $dbuser, $dbpass);
    }
    return $db;
}
