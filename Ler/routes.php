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
//08/24/2020 removed $_GET check so we can route user to home if nothing was specified
if (1==1) {
    $BASE = "templates";
    $path = array_key_first($_GET);
    //08/24/2020 added a default value to pass the next if condition
    if(!isset($path)){
        $path = "home";
    }
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
                //                //break;
            case "story/edit":
                include($BASE . "/storyform.php");
                break;
            case "story/delete":
                include ($BASE . "/delete_story.php");
                break;
            case "story/favorite":
                include ($BASE . "/favorite.php");
                break;
            case "mystories":
                $mystories = true;
                include ($BASE . "/stories.php");
                break;
            case "stories/progress":
                $myprogress = true;
                include ($BASE . "/stories.php");
                break;
            case "stories/favorites":
                $mybookmarks = true;
                include ($BASE . "/stories.php");
                break;
            case "stories":
                include ($BASE . "/stories.php");
                break;
            case "story/view":
                include($BASE . "/story.php");
                break;
            case "arc/edit":
            case "arc/create":
                include ($BASE . "/arcform.php");
                break;
            case "arc/view":
                include ($BASE . "/arc.php");
                break;
            case "arc/delete":
                include ($BASE . "/delete_arc.php");
                break;
            default:
                include ($BASE . "/home.php");
                break;
        }
    }
}
