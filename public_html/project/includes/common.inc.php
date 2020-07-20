<?php
session_start();

class Common {
    private $db;

    /*** Helper to get seconds between two dates. May only be accurate if $date1 is older than $date2.
     * @param $date1
     * @param null $date2 defaults to NOW
     * @return int
     * @throws Exception
     */
    public static function get_seconds_since_dates($date1, $date2 = NULL){
        if(!isset($date2)){
            $date2 = new DateTime();
        }
        if(!$date1 instanceof DateTime){
            //poor check for DT conversion, TODO make more robust.
            $date1 = new DateTime($date1);
        }
        return $date2->getTimestamp() - $date1->getTimestamp();
    }
    /*** Used as part of game validation to prevent cheating
     * @return int
     */
    public static function get_seconds_since_start(){
        //TODO update this to use get_seconds_since_dates()
        $started = Common::get($_SESSION, "started", false);
        if($started){
            try{
                if(is_string($started)) {
                    $started = new DateTime($started);
                }
                $now = new DateTime();
                if($started < $now) {
                    //https://stackoverflow.com/a/12520198
                    //$started can't be from the future
                    //$diff = $started->diff(new DateTime());
                    //changed to seconds, helps filter fake requests yet account for poor play
                    //$minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                    //return $minutes;
                    return $now->getTimestamp() - $started->getTimestamp();
                }
            }
            catch(Exception $e){
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
    public static function is_valid_game($isWin){
        $seconds = Common::get_seconds_since_start();
        error_log("Seconds $seconds");
        $min = 10;//Make sure game has been played a significant amount of time
        if(!$isWin){
            $min = 5;//hopefully the player survives longer than 5 seconds.
        }
        error_log("Is win $isWin");
        $max = 3600;//make sure it has been started within 60 mins
        //adjust the above constraints as necessary to reduce some basic cheats
        //a game shouldn't be finished in under a set amount of seconds and
        //a game shouldn't take an hour to complete
        error_log("min $min max $max");
        return ($seconds >= $min && $seconds <= $max);
    }
    public static function is_logged_in($redirect = true){
        if(Common::get($_SESSION, "user", false)){
            return true;
        }
        if($redirect){
            Common::flash("You must be logged in to access this page", "warning");
            die(header("Location: " . Common::url_for("login")));
        }
        else{
            return false;
        }
    }

    /*** System user ID used mostly as FK for various transactions.
     *    Cached in Session to reduce DB calls to fetch it. Populates on login.
     * @return mixed|string
     */
    public static function get_system_id(){
        return Common::get($_SESSION, "system_id", -1);
    }
    public static function get_user_id(){
        $id = -1;
        $user = Common::get($_SESSION, "user", false);
        if($user){
            $id = Common::get($user,"id", -1);
        }
        return $id;
    }
    public static function get_username(){
        $user = Common::get($_SESSION, "user", false);
        $name = "";
        if($user){
            $name = Common::get($user, "first_name", false);
            if(!$name){
                $name = Common::get($user, "email", false);//if this is false we have a bigger problem
                //or we didn't check if the user is logged in first
            }
        }
        return $name;
    }

    /*** Quick URL tool to get relative urls by passing desired php file name.
     * @param $lookup
     * @return mixed|string
     */
    public static function url_for($lookup){
        $path = __DIR__. "/../$lookup.php";
        //Heroku is deployed under an app folder and __DIR pulls full path
        //so we want to split the path on our doc root, then just grab
        //the contents after it
        $r = explode("public_html", $path, 2);
        if(count($r) > 1){
            return $r[1];
        }
        Common::flash("Error finding path", "danger");
        return "/project/index.php";
    }

    /*** Pass a single role to check if the logged in user has the role applied
     * @param $role
     * @return bool
     */
    public static function has_role($role){
        $user = Common::get($_SESSION, "user", false);
        if($user){
            $roles = Common::get($user, "roles", []);
            foreach($roles as $r){
                if($r["name"] == $role){
                    return true;
                }
            }
        }
        return false;
    }
    /*** Attempts to safely retrieve a key from an array, otherwise returns the default
     * @param $arr
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public static function get($arr, $key, $default = "") {
        if (is_array($arr) && isset($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    /*** Returns a shared instance of our PDO connection
     * @return PDO
     */
    public function getDB() {
        if (!isset($this->db)) {
            //Initialize all of these at once just to make the IDE happy
            $dbdatabase = $dbuser = $dbpass = $dbhost = NULL;
            require_once(__DIR__ . "/config.php");
            if (isset($dbhost) && isset($dbdatabase) && isset($dbpass) && isset($dbuser)) {
                $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
                $this->db = new PDO($connection_string, $dbuser, $dbpass);
            } else {
                //https://www.w3schools.com/php/func_error_log.asp
                error_log("Missing db config details");
            }
        }
        return $this->db;
    }

    /*** Used to store any type of message to the session to later be displayed with getFlashMessages()
     * @param $message String of the message
     * @param string $type Type used for styling (should match your css classes)
     */
    public static function flash($message, $type = "info") {
        if (!isset($_SESSION["messages"])) {
            $_SESSION["messages"] = [];
        }
        array_push($_SESSION["messages"], ["message"=>$message, "type"=>$type]);
        //error_log(var_export($_SESSION["messages"], true));
    }

    /*** Returns all messaged stored on the session.
     *  Calling this clears the messages from the session.
     * @return mixed
     */
    public static function getFlashMessages() {
        $messages = Common::get($_SESSION, "messages", []);
       //error_log("Get Flash Messages(): " . var_export($messages, true));
        $_SESSION["messages"] = [];
        return $messages;
    }

    public static function aggregate_stats_and_refresh(){
        //poor man's cronjob - run commands no sooner than specified delay
        $first = false;
        if(isset($_SESSION["last_sync"])){
            $last_sync = $_SESSION["last_sync"];
        }
        else{
            $last_sync = new DateTime();
            $_SESSION["last_sync"] = $last_sync;
            $first = true;
        }
        $seconds = Common::get_seconds_since_dates($last_sync);
        if($first || $seconds >= 120){//no sooner than every 2 mins
            //TODO aggregate user XP, Points, and Level
            $user_id = Common::get_user_id();
            $result = DBH::get_aggregated_stats($user_id);
            $result = Common::get($result, "data", false);
            if($result){
                error_log(var_export($result, true));
                $xp = Common::get($result, "XP", 0);
                $points = Common::get($result, "Points", 0);
                $wins = Common::get($result, "Wins", 0);
                $losses = Common::get($result, "Losses", 0);
                $level = (int)($xp/100)+1;//TODO implement different leveling system
                $result = DBH::update_user_stats($user_id, $level, $xp, $points, $wins, $losses);
                error_log(var_export($result, true));
                $_SESSION["user"]["experience"] = $xp;
                $_SESSION["user"]["level"] = $level;
                $_SESSION["user"]["points"] = $points;
                $_SESSION["user"]["wins"] = $wins;
                $_SESSION["user"]["losses"] = $losses;
                if(Common::get($result, "status", 400) == 200){
                    $_SESSION["last_sync"] = new DateTime();
                }

            }
            //update time
            $last_sync = new DateTime();
            $_SESSION["last_sync"] = $last_sync;
        }
    }
    public static function clamp($current, $min, $max) {
        //https://stackoverflow.com/a/35438811
        return max($min, min($max, $current));
    }
}

$common = new Common();
//make sure this is after we init common so it has access to it
require_once (__DIR__."/db_helper.php");
