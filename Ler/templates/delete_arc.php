<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
//make sure we're logged in
Utils::isLoggedIn(true);
$user = Utils::getLoggedInUser();
$author_id = $user->getId();
if($author_id > -1) {
    $arcs_service = $container->getArcs();
    $arc_id = Utils::get($_GET, "arc", -1);
    if ($arc_id > -1) {
        $result = $arcs_service->get_arc($arc_id);
        if (Utils::get($result, "status", "error") == "success") {
            $arc = Utils::get($result, "arc", array());
            $story_id = Utils::get($arc, "story_id", -1);
            if ($story_id > -1) {
                $story_service = $container->getStories();
                $result = $story_service->get_story($story_id);
                if (Utils::get($result, "status", "error") == "success") {
                    $story = Utils::get($result, "story");
                    $_author_id = Utils::get($story, "author");
                    if($_author_id == $author_id){
                        $result = $arcs_service->delete_arc($arc_id);
                        if(Utils::get($result, "status", "error") == "success"){
                            Utils::flash("Successfully deleted arc");
                        }
                    }
                    else{
                        Utils::flash("You can't delete an arc you didn't write.");
                    }
                }
            }
            else{
                Utils::flash("Couldn't find story for this arc");
            }
        }
    }
    else{
        Utils::flash("Failed to delete arc; arc wasn't found");
    }
}
if(isset($story_id) && $story_id > -1){
    die(header("Location: index.php?story/edit&story=$story_id"));
}
else{
    die(header("Location: index.php?stories"));
}