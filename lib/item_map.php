<?php
function load_active_items()
{
    $user_id = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT item_id from RM_Active_Items where user_id = :uid");
    try {
        $stmt->execute([":uid" => $user_id]);
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            foreach ($r as $item) {
                activate_item(se($item, "item_id", 0, false));
            }
        }
    } catch (PDOException $e) {
        error_log("Error fetching active items: " . var_export($e, true));
    }
}
function get_active_items()
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $items = [];
    $rope = (int)se($_SESSION, "rope", 0, false) > 0;
    if ($rope) {
        array_push($items, "R");
    }
    $first_aid = (float)se($_SESSION, "first_aid", 0, false) > 0;
    if ($first_aid) {
        array_push($items, "F");
    }
    $deterent = (float)se($_SESSION, "deterent", 0, false) > 0;
    if ($deterent) {
        array_push($items, "D");
    }
    return $items;
}
function activate_item($item_id)
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    //refer to Project/sql/011_insert_items.sql
    /**
     * (-1, "Rope", "Saves from one Pit Fall", 9999999, 5, ""),
(-2, "Wolf Deterent 25%", "Has a chance to prevent a wolf from appearing during a game session", 9999999, 15, ""),
(-3, "First Aid Kit 1", "Gain some extra points for the next rescued friend", 9999999, 1, ""),
(-4, "First Aid Kit 2", "Gain moderate extra points for the next rescued friend", 9999999, 2, ""),
(-5, "First Aid Kit 3", "Gain large amount of extra points for the next rescued friend", 9999999, 5, ""),
(-6, "Wolf Deterent 50%", "Has a chance to prevent a wolf from appearing during a game session",9999999, 25,"")
     */
    switch ($item_id) {
        case -1:
            $_SESSION["rope"] = 1;
            $_SESSION["rope_id"] = $item_id;
            break;
        case -2:
            $_SESSION["deterent"] = .25;
            $_SESSION["deterent_id"] = $item_id;
            break;
        case -3:
            $_SESSION["first_aid"] = .5;
            $_SESSION["first_aid_id"] = $item_id;
            break;
        case -4:
            $_SESSION["first_aid"] = .75;
            $_SESSION["first_aid_id"] = $item_id;
            break;
        case -5:
            $_SESSION["first_aid"] = 1.0;
            $_SESSION["first_aid_id"] = $item_id;
            break;
        case -6:
            $_SESSION["deterent"] = .5;
            $_SESSION["deterent_id"] = $item_id;
            break;
        default:
            error_log("Unhandled item_id $item_id");
            break;
    }
}
function has_rope()
{
    error_log("has rope");
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    //TODO ensure this works
    error_log("session: " . var_export($_SESSION, true));
    $rope = (int)se($_SESSION, "rope", 0, false) > 0;
    if ($rope) {
        $_SESSION["rope"] = 0;
        unset($_SESSION["rope"]);
        deactive_effect(se($_SESSION, "rope_id", 0, false));
        error_log("session: " . var_export($_SESSION, true));
    }
    return $rope;
}
function get_friend_value($original)
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $mod = (float)se($_SESSION, "first_aid", 0, false);
    if ($mod > 0) {
        $original += ($original * $mod);
        unset($_SESSION["first_aid"]);
        deactive_effect(se($_SESSION, "first_aid_id", -1, false));
    }
    return $original;
}
function deter_wolf()
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $chance = round((float)se($_SESSION, "deterent", 1, false) * 100);
    return random_int(0, 100) < $chance;
}
function clear_deterrent()
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION["deterent"])) {
        unset($_SESSION["deterent"]);
        deactive_effect(se($_SESSION, "deterent_id", -1, false));
    }
}
function deactive_effect($item_id)
{
    $db = getDB();
    $stmt = $db->prepare("UPDATE RM_Active_Items set quantity = quantity - 1 WHERE item_id = :iid AND user_id = :uid");
    $ran = false;
    try {
        $stmt->execute([":iid" => $item_id, ":uid" => get_user_id()]);
        $ran = true;
    } catch (PDOException $e) {
        error_log("Error deactivating item: " . var_export($e, true));
    }
    if ($ran) {
        $stmt = $db->prepare("DELETE FROM RM_Active_Items WHERE quantity <= 0");
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting empty active items: " . var_export($e, true));
        }
    }
}