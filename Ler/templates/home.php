<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}

?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Welcome to Ler!</h1>
        <h5>Where you can come <code>to read</code> interactive stories!</h5>
        <p class="lead">
            This project lets writers create interactive stories. <br>
            The stories will contain Arcs (or sections of plot), then each Arc will give the reader 1-4 decisions.<br>
            Based on the decision selected it'll go to a different Arc (or line of plot).<br>
            The reader can continue until they reach 1 of the many endings.<br>
        </p>
    </div>
</div>
<?php
$stories_service = $container->getStories();
$top = $stories_service->get_top_stories();
$top = Utils::get($top, "stories");
$new = $stories_service->get_new_stories();
$new = Utils::get($new, "stories");
$updated = $stories_service->get_updated_stories();
$updated = Utils::get($updated, "stories");
?>
<div class="row">
    <div class="col">
        <h4>Top Stories</h4>
        <?php foreach($top as $story):?>
            <?php
            //used for my stories since there's no join on user table to get name
            //we'll cheatsy it here
            if(isset($user) && !isset($story['username'])){
                if(Utils::get($story, 'author') == $user->getId()){
                    $story['username'] = $user->getUsername();
                }
            }
            ?>
            <?php include(__DIR__.'/../partials/story.partial.php');?>
        <?php endforeach; ?>
    </div>
    <div class="col">
        <h4>Newest Stories</h4>
        <?php foreach($new as $story):?>
            <?php
            //used for my stories since there's no join on user table to get name
            //we'll cheatsy it here
            if(isset($user) && !isset($story['username'])){
                if(Utils::get($story, 'author') == $user->getId()){
                    $story['username'] = $user->getUsername();
                }
            }
            ?>
            <?php include(__DIR__.'/../partials/story.partial.php');?>
        <?php endforeach; ?>
    </div>
    <div class="col">
        <h4>Recently Update Stories</h4>
        <?php foreach($updated as $story):?>
            <?php
            //used for my stories since there's no join on user table to get name
            //we'll cheatsy it here
            if(isset($user) && !isset($story['username'])){
                if(Utils::get($story, 'author') == $user->getId()){
                    $story['username'] = $user->getUsername();
                }
            }
            ?>
            <?php include(__DIR__.'/../partials/story.partial.php');?>
        <?php endforeach; ?>
    </div>
</div>