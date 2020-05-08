<?php
if(isset($_GET['story'])){
    $story_id = $_GET['story'];
    $user = Utils::getLoggedInUser();
    $user_id = -1;//anonymous user
    if($user) {
        $user_id = $user->getId();
    }
    if($user_id > -1 && $story_id > -1) {
        if(!isset($favorites_service)){
            $favorites_service = $container->getFavorites();
        }
        if(Utils::get($_GET, "favorite", false)){
            $result = $favorites_service->delete_favorite($user_id, $story_id);
        }
        else {
            $result = $favorites_service->create_favorite($user_id, $story_id);
        }
    }
    else{
        Utils::flash("You must log in to favorite stories");
    }
}
if(isset($_SERVER['HTTP_REFERER'])){
    $referer = $_SERVER['HTTP_REFERER'];
    $referer = explode("index.php", $referer);
    $referer = $referer[1];
    if(isset($referer)){
        die(header("Location: index.php" . $referer));
    }
    else{
        die(header("Location: index.php?stories"));
    }
}