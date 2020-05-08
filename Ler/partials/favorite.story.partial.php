<?php
if(!isset($user_id)){
    $user = Utils::getLoggedInUser();
    $user_id = -1;//anonymous user
    if($user) {
        $user_id = $user->getId();
    }
}
$favorites_service = $container->getFavorites();
$result = $favorites_service->get_favorite($user_id, $story_id);
$fav = Utils::get($result, "favorite", false);
if($fav){
    $class = "fas";
}
else{
    $class = "far";
}
?>
<a id="favorite" href="index.php?story/favorite&story=<?php echo $story_id;?>&favorite=<?php echo $fav;?>"><i class="<?php echo $class;?> fa-heart"></i></a>
