<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
if(isset($_GET['story'])){
    $story_id = $_GET['story'];
    $story_service = $container->getStories();
    $result = $story_service->get_story($story_id);
    if($result['status'] == 'success'){
        $story = $result['story'];
    }
    else{
        Utils::flash(Utils::get($result, "message") . ": " . Utils::get($result, "errorInfo"));
    }
}
?>
<?php if(isset($story) && !empty($story)):?>
<div class="card">
    <div class="card-body">
        <h4 class="card-title">
            <?php Utils::show($story, "title");?>
        </h4>
        <h6>by <?php Utils::show($story, "username");?></h6>
        <pre class="card-body" style="overflow: auto;">
            <?php echo htmlspecialchars_decode(Utils::get($story,"summary"));?>
        </pre>
        <footer>
            <a class="btn btn-success"
            href="index.php?arc/view&arc=<?php Utils::show($story, "starting_arc");?>"
            >Begin</a>
        </footer>
    </div>
</div>
<?php else:?>
<div class="alert alert-danger">There was an error loading the story.</div>
<?php endif;?>
