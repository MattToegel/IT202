<?php
// This is an internal API endpoint to receive data and do something with it
// this is not a standalone page
//Note: no nav.php here because this is a temporary stop, it's not a user page
require(__DIR__ . "/../../../lib/functions.php");
session_start();
/*if(isset($_SESSION["query"])){
    $d = $_SESSION["query"];
    $now;
    if(abs($now-$d) > 1000){
        echo json_encode([]);
        die();
    }
}*/
if (isset($_GET["query"])) {
    //TODO implement purchase logic (for now it's all free)
    $name = $_GET["query"];
    $db = getDB();
    $query = "SELECT * FROM `IT202-S24-Brokers` WHERE name like :name";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":name" => "%$name%"]);
        $r = $stmt->fetchAll();
        if ($r) {
            echo json_encode($r);
        } else {
            echo json_encode([]);
        }
    } catch (PDOException $e) {
        error_log("Error purchasing broker: " . var_export($e, true));
    }
}
