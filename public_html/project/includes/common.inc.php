<?php
session_start();
class Common
{
    private $db;
    /*** Attempts to safely retrieve a key from an array, otherwise returns the default
     * @param $arr
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function get($arr, $key, $default = "")
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    /*** Returns a shared instance of our PDO connection
     * @return PDO
     */
    public function getDB()
    {
        if (!isset($this->db)) {
            require_once(__DIR__ . "/config.php");
            //TODO ignore the editor errors for the variables, they'll be pulled from config.php
            $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
            $this->db = new PDO($connection_string, $dbuser, $dbpass);
        }
        return $this->db;
    }

    /*** Used to store any type of message to the session to later be displayed with getFlashMessages()
     * @param $message String of the message
     * @param string $type Type used for styling (should match your css classes)
     */
    public function flash($message, $type="info"){
        if(!isset($_SESSION["messages"])){
            $_SESSION["messages"] = [];
        }
        array_push($_SESSION["messages"], [$message, $type]);
    }

    /*** Returns all messaged stored on the session.
     *  Calling this clears the messages from the session.
     * @return mixed
     */
    public function getFlashMessages(){
        $messages = $_SESSION["messages"];
        $_SESSION["messages"] = [];
        return $messages;
    }
}
