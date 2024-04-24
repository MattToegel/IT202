<?php

/**
 * Passing $redirect as true will auto redirect a logged out user to the $destination.
 * The destination defaults to login.php
 */
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        //if this triggers, the calling script won't receive a reply since die()/exit() terminates it
        flash("You must be logged in to view this page", "warning");
        redirect($destination);
    }
    return $isLoggedIn;
}
function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
function get_username()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}

function get_points()
{
    if (is_logged_in()) {
        $db = getDB();
        $query = "SELECT SUM(point_change) as points FROM `IT202-S24-Points` WHERE user_id = :user_id";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute([":user_id" => get_user_id()]);
            $r = $stmt->fetch();
            if ($r) {
                return (int)$r["points"];
            }
        } catch (Exception $e) {
            error_log("Error fetching points: " . var_export($e, true));
            flash("Error fetching points", "danger");
        }
        return 0;
    }
}