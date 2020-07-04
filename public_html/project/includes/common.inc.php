<?php
session_start();

class Common {
    private $db;
    public static function get_seconds_since_start(){
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
        $messages = $_SESSION["messages"];
       //error_log("Get Flash Messages(): " . var_export($messages, true));
        $_SESSION["messages"] = [];
        return $messages;
    }
}

$common = new Common();
//make sure this is after we init common so it has access to it
require_once (__DIR__."/db_helper.php");
