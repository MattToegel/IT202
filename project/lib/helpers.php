<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
//added placeholder for passing "type" of alert so we can style it later. Will implement in another feature
function flash($msg, $type = 'info') {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//end flash

function getState($n) {
    switch ($n) {
        case 0:
            echo "Stasis";
            break;
        case 1:
            echo "Incubating";
            break;
        case 2:
            echo "Hatching";
            break;
        case 3:
            echo "Hatched";
            break;
        case 4:
            echo "Expired";
            break;
        default:
            echo "Unsupported state: " . safer_echo($n);
            break;
    }
}

function getURL($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/IT202/project/$path";
}

function getBalance() {
    if (is_logged_in() && isset($_SESSION["user"]["balance"])) {
        return $_SESSION["user"]["balance"];
    }
    return 0;
}

function calcNextEggCost() {
    if (is_logged_in()) {
        $db = getDB();
        $stmt = $db->prepare("SELECT count(id) as eggs from F20_Eggs where user_id = :id");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result["eggs"])) {
            $c = (int)$result["eggs"];
            $base_cost = 10;
            return $c * $base_cost; // first is free
        }
    }
    return -1;//-1 will be invalid
}
function paginate($query, $params, $page,$per_page){
    $db = getDB();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if($result){
        $total = (int)$result["total"];
    }
    global $total_pages;
    $total_pages = ceil($total / $per_page);
    global $offset;
    $offset = ($page-1) * $per_page;
}
?>
