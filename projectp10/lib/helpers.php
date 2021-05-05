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
    return $_SERVER["CONTEXT_PREFIX"] . "/IT202/projectp10/$path";
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

function changePoints($user_id, $points, $reason, $src_id = -1)
{
    $db = getDB();
    //$query = "INSERT INTO tfp_pointhistory (user_id, points_change, reason) VALUES (:uid, :change, :reason)";
    $query = "INSERT INTO tfp_pointhistory (user_id, points_change, reason, src_id) VALUES(:id1, :c1, :r, :id2), (:id2, :c2, :r, :id1)";
    if($points >= 0){
        $id1 = $src_id;
        $id2 = $user_id;
        $points *= -1;
    }
    else if($points < 0){
        $id1 = $user_id;
        $id2 = $src_id;
    }
    

    $stmt = $db->prepare($query);
    $r = $stmt->execute(
        [":id1" => $id1, 
        ":id2" => $id2,
        ":c1" => $points, 
        ":c2" => ($points*-1),
        ":r" => $reason]);
    if ($r) {
        $query = "UPDATE tfp_userstats set points = IFNULL((SELECT sum(points_change) FROM tfp_pointhistory where user_id = :uid),0) WHERE user_id = :uid";
        $stmt = $db->prepare($query);
        $r = $stmt->execute([":uid" => $user_id]);

        //refresh session data
        if(safe_get($_SESSION, "user", false) && safe_get($_SESSION["user"], "points", false)){
            $_SESSION["user"]["points"] = get_points_balance();
        }
        return $r;
    }
    return false;
}
/*** Helper to get seconds between two dates. May only be accurate if $date1 is older than $date2.
 * @param $date1
 * @param null $date2 defaults to NOW
 * @return int
 * @throws Exception
 */
function get_seconds_since_dates($date1, $date2 = NULL)
{
    if (!isset($date2)) {
        $date2 = new DateTime();
    }
    if (!$date1 instanceof DateTime) {
        //poor check for DT conversion, TODO make more robust.
        $date1 = new DateTime($date1);
    }
    return $date2->getTimestamp() - $date1->getTimestamp();
}
/*** Used as part of game validation to prevent cheating
 * @return int
 */
function get_seconds_since_start()
{
    //TODO update this to use get_seconds_since_dates()
    $started = safe_get($_SESSION, "started", false);
    if ($started) {
        try {
            if (is_string($started)) {
                $started = new DateTime($started);
            }
            $now = new DateTime();
            if ($started < $now) {
                //https://stackoverflow.com/a/12520198
                //$started can't be from the future
                //$diff = $started->diff(new DateTime());
                //changed to seconds, helps filter fake requests yet account for poor play
                //$minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                //return $minutes;
                return $now->getTimestamp() - $started->getTimestamp();
            }
        } catch (Exception $e) {
            //invalid date
            error_log($e->getMessage());
        }
    }
    return -1;
}

/*** Basis of anti cheating check, still WIP
 * @param $isWin
 * @return bool
 */
function is_valid_game($isWin)
{
    $seconds = get_seconds_since_start();
    error_log("Seconds $seconds");
    $min = 10; //Make sure game has been played a significant amount of time
    if (!$isWin) {
        $min = 5; //hopefully the player survives longer than 5 seconds.
    }
    //error_log("Is win $isWin");
    $max = 3600; //make sure it has been started within 60 mins
    //adjust the above constraints as necessary to reduce some basic cheats
    //a game shouldn't be finished in under a set amount of seconds and
    //a game shouldn't take an hour to complete
    error_log("min $min max $max");
    return ($seconds >= $min && $seconds <= $max);
}

function update_experience($user_id){
    $db = getDB();
    $query = "UPDATE tfp_userstats set experience = (select (SUM(IFNULL(score, 0)) * 10) FROM tfp_scores WHERE user_id = :uid) WHERE user_id = :uid";
     $stmt = $db->prepare($query);
        $r = $stmt->execute([":uid" => $user_id]);
        return $r;
}

function get_points_balance(){
    $uid = get_user_id();
    $db = getDB();
    $query = "SELECT IFNULL(points,0) as `points` from tfp_userstats where user_id = :id";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":id"=>$uid]);
    if($r){
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($stats["points"])){
            return (int)$stats["points"];
        }
    }
    return 0;
}