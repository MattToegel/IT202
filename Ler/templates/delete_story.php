<?php
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
$story_id =  $story_id = Utils::get($_GET, "story", -1);
$user = Utils::getLoggedInUser(true);
$author_id = $user->getId();
if($story_id > -1 && $author_id > -1){
    $stories_service = $container->getStories();
    $result = $stories_service->delete_story($story_id, $author_id);
    Utils::flash(Utils::get($result, "message"));
}
else{
    Utils::flash("Error deleting story");
}
die(header("Location: index.php?mystories"));
?>