<?php //used to get rid of the IDE errors, though this file should never be called separately
if(!isset($story)){
    $story = array();
}
if(!isset($user_id)){
    $author_id = -1;
}
?>
<div class="card">
    <div class="card-body">
    <h3 class="lead"><?php Utils::show($story, "title");?></h3>
    <h6>By <?php Utils::show($story,"username");?></h6>
    <div class="card-text"><i class="fas fa-book-open"></i>&nbsp;<?php Utils::show($story,"summary","", 100);?></div>
    <div>
        <?php if($author_id == Utils::get($story, "author")):?>
            <a class="btn btn-secondary"
                href="<?php echo "index.php?story/edit&story=" . Utils::get($story, "story_id");?>">
                Edit
            </a>
            <a class="btn btn-primary"
               href="<?php echo "index.php?arc/create&story=" . Utils::get($story, "story_id");?>">Create Arc</a>
        <?php endif; ?>
        <a class="btn btn-success"
            href="<?php echo "index.php?story/view&story=" . Utils::get($story, "story_id");?>">Start Reading</a>
        <?php if($author_id == Utils::get($story, "author")):?>
            <a class="btn btn-danger"
               href="<?php echo "index.php?story/delete&story=" . Utils::get($story, "story_id");?>">Delete</a>
        <?php endif;?>
    </div>
    </div>
</div>
