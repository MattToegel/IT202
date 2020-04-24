<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
if(isset($_GET['story'])){
    $story_id = $_GET['story'];
    $story_service = $container->getStories();
    $story_service->get_story($story_id);
}