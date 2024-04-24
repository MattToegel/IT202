<?php
// This is an internal API endpoint to receive data and do something with it
// this is not a standalone page
//Note: no nav.php here because this is a temporary stop, it's not a user page
require(__DIR__ . "/../../../lib/functions.php");
session_start();
if (isset($_GET["broker_id"]) && is_logged_in()) {
    $user_points = get_points();
    if ($user_points < 10) {
        error_log("Can't afford");
        flash("You can't afford this right now", "warning");
        redirect("brokers.php");
    }
    //TODO implement purchase logic (for now it's all free)
    $db = getDB();
    $query = "INSERT INTO `IT202-S24-UserBrokers` (user_id, broker_id) VALUES (:user_id, :broker_id)";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => get_user_id(), ":broker_id" => $_GET["broker_id"]]);

        //deduct cost
        $change = ["point_change" => -10, "user_id" => get_user_id()];
        $result = insert("`IT202-S24-Points`", $change);
        error_log("Points update: " . var_export($result, true));
        flash("Congrats you purchased the broker", "success");
        redirect("my_brokers.php");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] === 1062) {
            flash("This broker isn't available", "danger");
        } else {
            flash("Unhandled error occurred", "danger");
        }
        error_log("Error purchasing broker: " . var_export($e, true));
    }
}

//for now I'll redirect, but if I later use AJAX I need to send a reply instead
redirect("brokers.php");
