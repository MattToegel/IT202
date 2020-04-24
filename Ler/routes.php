<?php
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

//attempt at crude router
if (count($_GET) > 0) {
    $BASE = "templates";
    $path = array_key_first($_GET);
    if (isset($path)) {
        //Utils::flash($path);
        switch ($path) {
            case "home":
                include($BASE . "/home.php");
                break;
            case "login":
                include($BASE . "/login.php");
                break;
            case "profile":
                include($BASE . "/profile.php");
                break;
            case "logout":
                include($BASE . "/logout.php");
                break;
            case "story/create": //for now, fall down to next case since it's the same
                //include("storyform.php");
                //break;
            case "story/edit":
                include($BASE . "/storyform.php");
                break;
            case "stories":
                include ($BASE . "/stories.php");
                break;
            case "arc/edit":
            case "arc/create":
                include ($BASE . "/arcform.php");
                break;
            case "arc/view":
                include ($BASE . "/arc.php");
                break;
            default:
                break;
        }
    }
}