<?php
session_start(); //we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
/*function is_logged_in(){
    return isset($_SESSION["user"]);
}*/
function is_logged_in($redirect = true)
{
    if (safe_get($_SESSION, "user", false)) {
        return true;
    }
    if ($redirect) {
        flash("You must be logged in to access this page", "warning");
        die(header("Location: " . getUrl("login.php")));
    } else {
        return false;
    }
}
function has_role($role)
{
    if (is_logged_in(false) && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}
function get_username()
{
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email()
{
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id()
{
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}
function safer_echo($var)
{
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}
//for flash feature
function flash($msg)
{
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }
}

function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//end flash
function getURL($path)
{
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    //edit just the appended path
    return $_SERVER["CONTEXT_PREFIX"] . "/IT202/projectp5/$path";
}


/*** Attempts to safely retrieve a key from an array, otherwise returns the default
 * @param $arr
 * @param $key
 * @param string $default
 * @return mixed|string
 */
function safe_get($arr, $key, $default = "")
{
    if (is_array($arr) && isset($arr[$key])) {
        return $arr[$key];
    }
    return $default;
}

function changePoints($user_id, $points, $reason){
    $db = getDB();
    $query = "INSERT INTO tfp_pointhistory (user_id, points_change, reason) VALUES (:uid, :change, :reason)";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":uid"=>$user_id, ":change"=>$points, ":reason"=>$reason]);
    if($r){
        $query = "UPDATE tfp_userstats set points = IFNULL((SELECT sum(points_change) FROM tfp_pointhistory where user_id = :uid),0) WHERE user_id = :uid";
        $stmt = $db->prepare($query);
        $r = $stmt->execute([":uid"=>$user_id]);
        return $r;
    }
    return false;
}
