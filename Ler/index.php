<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("bootstrap.php");

/***
 * Pass in a file name to include the file (so php executes) and return the compiled results
 * @param $file
 * @return false|string
 */
function fetch($file){
    //ob_start();
    include($file);
    return ob_get_clean();
}
/***
 * array_key_first only available in php >7.3.0 so we poly fill it
 */
if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}
?>
<div class="container">
<nav>
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link active" href="index.php?">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php?login">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php?profile">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php?logout">Logout</a>
        </li>
    </ul>
</nav>
<div class="row">

    <?php foreach(Utils::getMessages() as $msg):?>
        <div class="row bg-secondary">
            <?php echo $msg;?>
        </div>
    <?php endforeach; ?>
</div>
<?php
//attempt at crude router
if(count($_GET) > 0){
    $path = array_key_first($_GET);
    if(isset($path)){
        Utils::flash($path);
        switch($path){
            case "home":
                echo include("home.php");
                break;
            case "login":
                echo include("login.php");
                break;
            case "profile":
                echo include("profile.php");
                break;
            case "logout":
                echo include("logout.php");
                break;
            case "story/create":
                echo include("create_story.php");
                break;
            default:
                break;
        }
    }
}
?>
</div>
