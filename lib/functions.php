<?php
require_once(__DIR__ . "/db.php");
$BASE_PATH = '/Project/';//This is going to be a helper for redirecting to our base project path since it's nested in another folder
function se($v, $k = null, $default = "", $isEcho = true) {
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
function sanitize_email($email = "") {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "") {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
//User Helpers
function is_logged_in() {
    return isset($_SESSION["user"]); //se($_SESSION, "user", false, false);
}
function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
function get_username() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
//flash message system
function flash($msg = "", $color = "info") {
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
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
//end flash message system
/**
 * Generates a unique string based on required length.
 * The length given will determine the likelihood of duplicates
 */
function get_random_str($length) {
    //https://stackoverflow.com/a/13733588
    //$bytes = random_bytes($length / 2);
    //return bin2hex($bytes);

    //https://stackoverflow.com/a/40974772
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 36)), 0, $length);
}
/**
 * Will fetch the account of the logged in user, or create a new one if it doesn't exist yet.
 * Exists here so it may be called on any desired page and not just login
 * Will populate/refresh $_SESSION["user"]["account"] regardless.
 * Make sure this is called after the session has been set
 */
function get_or_create_account() {
    if (is_logged_in()) {
        //let's define our data structure first
        //id is for internal references, account_number is user facing info, and balance will be a cached value of activity
        $account = ["id" => -1, "account_number" => false, "balance" => 0, "quarry_vouchers" => 0];
        //this should always be 0 or 1, but being safe
        $query = "SELECT id, account, balance, quarry_vouchers from Accounts where user_id = :uid LIMIT 1";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":uid" => get_user_id()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //account doesn't exist, create it
                $created = false;
                //we're going to loop here in the off chance that there's a duplicate
                //it shouldn't be too likely to occur with a length of 12, but it's still worth handling such a scenario

                //you only need to prepare once
                $query = "INSERT INTO Accounts (account, user_id) VALUES (:an, :uid)";
                $stmt = $db->prepare($query);
                $user_id = get_user_id(); //caching a reference
                $account_number = "";
                while (!$created) {
                    try {
                        $account_number = get_random_str(12);
                        $stmt->execute([":an" => $account_number, ":uid" => $user_id]);
                        $created = true; //if we got here it was a success, let's exit
                        flash("Welcome! Your account has been created successfully", "success");
                    } catch (PDOException $e) {
                        $code = se($e->errorInfo, 0, "00000", false);
                        //if it's a duplicate error, just let the loop happen
                        //otherwise throw the error since it's likely something looping won't resolve
                        //and we don't want to get stuck here forever
                        if (
                            $code !== "23000"
                        ) {
                            throw $e;
                        }
                    }
                }
                //loop exited, let's assign the new values
                $account["id"] = $db->lastInsertId();
                $account["account_number"] = $account_number;
            } else {
                //$account = $result; //just copy it over
                $account["id"] = $result["id"];
                $account["account_number"] = $result["account"];
                $account["balance"] = $result["balance"];
                $account["quarry_vouchers"] = $result["quarry_vouchers"];
            }
        } catch (PDOException $e) {
            flash("Technical error: " . var_export($e->errorInfo, true), "danger");
        }
        $_SESSION["user"]["account"] = $account; //storing the account info as a key under the user session
        //Note: if there's an error it'll initialize to the "empty" definition around line 84

    } else {
        flash("You're not logged in", "danger");
    }
}
function get_account_balance() {
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        return (int)se($_SESSION["user"]["account"], "balance", 0, false);
    }
    return 0;
}
function get_user_account_id() {
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        return (int)se($_SESSION["user"]["account"], "id", 0, false);
    }
    return 0;
}
function get_vouchers() {
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        //flash(var_export($_SESSION, true), "warning");
        return (int)se($_SESSION["user"]["account"], "quarry_vouchers", 0, false);
    }
    return 0;
}
/**
 * Points should be passed as a positive value.
 * $src should be where the points are coming from
 * $dest should be where the points are going
 */
function change_points($points, $reason, $src = -1, $dest = -1, $memo = "") {
    //I'm choosing to ignore the record of 0 point transactions

    if ($points > 0) {
        $query = "INSERT INTO Points_History (account_src, account_dest, point_change, reason, memo) 
            VALUES (:acs, :acd, :pc, :r,:m), 
            (:acs2, :acd2, :pc2, :r, :m)";
        //I'll insert both records at once, note the placeholders kept the same and the ones changed.
        $params[":acs"] = $src;
        $params[":acd"] = $dest;
        $params[":r"] = $reason;
        $params[":m"] = $memo;
        $params[":pc"] = ($points * -1);

        $params[":acs2"] = $dest;
        $params[":acd2"] = $src;
        $params[":pc2"] = $points;
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            refresh_account_balance();
        } catch (PDOException $e) {
            flash("Transfer error occurred: " . var_export($e->errorInfo, true), "danger");
        }
    }
}
function refresh_account_balance() {
    if (is_logged_in()) {
        //cache account balance via Point_History history
        $query = "UPDATE Accounts set balance = (SELECT IFNULL(SUM(point_change), 0) from Points_History WHERE account_src = :src) where id = :src";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":src" => get_user_account_id()]);
            get_or_create_account(); //refresh session data
        } catch (PDOException $e) {
            flash("Error refreshing account: " . var_export($e->errorInfo, true), "danger");
        }
    }
}
function refresh_last_login() {
    if (is_logged_in()) {
        //check if last_login is today
        $query = "SELECT date(last_login) = date(current_timestamp) as same_day from Users where id = :uid";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":uid" => get_user_id()]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $isSameDay = (int)se($r, "same_day", 0, false);
                if ($isSameDay === 0) {
                    change_points(1, "login_bonus", -1, get_user_account_id());
                    flash("You received a login bonus of 1 point!", "success");
                }
            }
        } catch (PDOException $e) {
            error_log("Unknown error during date check: " . var_export($e->errorInfo, true));
        }
        //update the timestamp
        $query = "UPDATE Users set last_login = current_timestamp Where id = :uid";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":uid" => get_user_id()]);
        } catch (PDOException $e) {
            error_log("Unknown error during date check: " . var_export($e->errorInfo, true));
        }
    }
}
/** Used to retain existing query parameters when changing pages during pagination
 * It'll update the "page" key in the $_GET array and pass the array to http_build_query() to generate the query string.
 */
function pagination_filter($newPage) {
    $_GET["page"] = $newPage;
    //php.net/manual/en/function.http-build-query.php
    return se(http_build_query($_GET));
}
/** Runs two queries, one to get the total_records for the potentially filtered data, and the other to return the paginated data */
function paginate($query, $params = [], $records_per_page = 5) {

    global $total_records; //used for pagination display after this function
    global $page; //used for pagination display after this function
    //what page is the user on?
    //ensure we're not less than page 1 (page 1 is so it makes sense to the user, we'll convert it to 0)
    $page = se($_GET, "page", 1, false);
    if ($page < 1) {
        $page = 1;
    }

    $db = getDB();

    //get the total records for the current filtered (if applicable) data
    //this will get the get the part of the query after FROM
    $t_query = "SELECT count(1) as `total` FROM " . explode(" FROM ", $query)[1];
    //var_dump($t_query);
    $stmt = $db->prepare($t_query);
    try {
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $total_records = (int)se($result, "total", 0, false);
        }
    } catch (PDOException $e) {
        error_log("Error getting total records: " . var_export($e->errorInfo, true));
    }
    $offset = ($page - 1) * $records_per_page;
    //get the data 
    $query .= " LIMIT :offset, :limit";
    //IMPORTANT: this is required for the execute to set the limit variables properly
    //otherwise it'll convert the values to a string and the query will fail since LIMIT expects only numerical values and doesn't cast
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //END IMPORTANT
    $stmt = $db->prepare($query);
    $results = [];
    try {
        $params[":offset"] = $offset;
        $params[":limit"] = $records_per_page;
        //var_dump($params);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<pre>";
        var_dump($e);
        echo "</pre>";
        error_log("Error getting records: " . var_export($e->errorInfo, true));
        flash("There was a problem with your request, please try again", "warning");
    }
    return $results;
}
/** Updates a daily tally of score for the user based on points acquired for mining on the current date */
function update_score() {
    //note: I'm diverging from the traditional score table due to poor planning ahead.
    //Originally it was supposed to be 1 record per acquired score, but I couldn't think of how my data/system would do that fairly
    //Instead I'm going to record a running total of "today's" score based on the sum of points acquired from mining
    //Due to that, I need to have the below few queries to verify if it's still "today" or not since "created" isn't a unique key or composite unique key
    // to rely on INSERT or UPDATE.
    // This will make me need to rethink how to show daily, monthy, and lifetime scoreboards a bit so likely will not fit the arcade project as I implement it
    // But the general implementation is *much* easier than what I'll be doing

    //going to split this into multiple queries, even though some can be condensed
    // 1) SUM the points from today's mining events
    $query = "SELECT IFNULL(SUM(point_change), 0) as total FROM Points_History p where p.account_src = :a AND reason = 'mining' AND date(created) = current_date()";
    $db = getDB();
    $stmt = $db->prepare($query);
    $points_today = 0;
    try {
        $stmt->execute([":a" => get_user_account_id()]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $points_today = (int)se($r, "total", 0, false);
        }
    } catch (PDOException $e) {
        error_log("Error fetching points sum: " . var_export($e->errorInfo, true));
    }
    error_log("Checking $points_today");
    if ($points_today > 0) {
        $user = get_user_id();
        //should be able to safely ignore 0 points, no need to record "nothing"
        //Note: due to lack of permissions we can't use curr_date(), however current_date() works fine

        // 2) check if we need to update or create a record in the scores table
        $query = "SELECT count(1) as rec from Scores where user_id = :uid AND date(created) = current_date()";
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":uid" => $user]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $t = (int)se($r, "rec", -1, false);
                if ($t > 0) {
                    // 3) update the score for today
                    $query = "UPDATE Scores set score = :p WHERE user_id = :uid AND date(created) = current_date()";
                    $stmt = $db->prepare($query);
                    try {
                        $stmt->execute([":uid" => $user, ":p" => $points_today]);
                        error_log("Updated Score for $user to $points_today");
                    } catch (PDOException $e) {
                        error_log("Error updating today's score for $user with $points_today: " . var_export($e->errorInfo, true));
                    }
                    return;
                }
            }
            // 3) create a new entry for holding today's score
            $query = "INSERT INTO Scores (user_id, score) VALUES (:uid,:p)";
            $stmt = $db->prepare($query);
            try {
                $stmt->execute([":uid" => $user, ":p" => $points_today]);
                error_log("Created Score for $user to $points_today");
            } catch (PDOException $e) {
                error_log("Error creating record for today's score for $user with $points_today: " . var_export($e->errorInfo, true));
            }
            return;
        } catch (PDOException $e) {
            error_log("Error checking today's score record: " . var_export($e->errorInfo, true));
        }
    }
}