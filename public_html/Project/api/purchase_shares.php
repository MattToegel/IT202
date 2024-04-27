<?php
// This is an internal API endpoint to receive data and do something with it
// this is not a standalone page
//Note: no nav.php here because this is a temporary stop, it's not a user page
require(__DIR__ . "/../../../lib/functions.php");
session_start();
if (isset($_POST["broker_id"]) && is_logged_in()) {
    $user_points = get_points();
    $db = getDB();
    $symbol = se($_POST, "symbol", "", false);
    $shares = se($_POST, "shares", 1, false);
    $broker_id = se($_POST, "broker_id", -1, false);
    $user_id = get_user_id();
    $hasError = false;
    if (empty($symbol)) {
        flash("Symbol must be provided", "danger");
        $hasError = true;
    }
    if ($shares <= 0) {
        flash("Shares must be a positive value", "danger");
        $hasError = true;
    }
    if ($broker_id < 1) {
        flash("Invalid broker", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //verify bulk
        $query = "SELECT 
    (SELECT price FROM `IT202-S24-Stocks` s WHERE symbol = :symbol ORDER BY latest desc limit 1) as price, 
    (SELECT broker_id FROM `IT202-S24-UserBrokers` WHERE user_id = :user_id and broker_id = :broker_id limit 1) as broker_id
    FROM dual limit 1";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute([":symbol" => $symbol, ":user_id" => get_user_id(), ":broker_id" => $broker_id]);
            $r = $stmt->fetch();
            if ($r) {
                $price = ceil($r["price"]);
                $broker_id = $r["broker_id"];
            }
        } catch (PDOException $e) {
            error_log("Error getting bulk info: " . var_export($e, true));
            flash("Error getting purchase details", "danger");
            $hasError = true;
        }
    }
    if ($user_points < ($price * $shares)) {
        error_log("Can't afford");
        flash("You can't afford this right now", "warning");
        $hasError = true;
    }
    if (!$hasError) {
        //purchase
        $query = "UPDATE `IT202-S24-Portfolios` p SET shares = shares + :shares WHERE broker_id = :broker_id and symbol = :symbol and broker_id in
        (SELECT broker_id FROM `IT202-S24-UserBrokers` b where b.broker_id= :broker_id and b.user_id=:user_id)";
        try {
            $stmt = $db->prepare($query);
            $stmt->bindValue(":shares", $shares, PDO::PARAM_INT);
            $stmt->bindValue(":broker_id", $broker_id);
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":symbol", $symbol);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $change = ["point_change" => - ($shares * $price), "user_id" => get_user_id()];
                $result = insert("`IT202-S24-Points`", $change);
                flash("Purchased Shares for Broker", "success");
            } else {
                flash("Purchase not successful", "warning");
            }
        } catch (PDOException $e) {
            error_log("Error updating shares: " . var_export($e, true));
            flash("Error updating shares", "danger");
        }
    }
}

//for now I'll redirect, but if I later use AJAX I need to send a reply instead
redirect("broker.php?id=$broker_id");
