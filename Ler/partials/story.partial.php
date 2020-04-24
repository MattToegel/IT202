<?php //used to get rid of the IDE errors, though this file should never be called separately
if(!isset($story)){
    $story = array();
}
if(!isset($author_id)){
    $author_id = -1;
}
?>
<div class="card">
    <div class="card-body">
    <h5 class="bg-primary"><?php Utils::show($story, "title");?></h5>
    <div class="card-text">Summary: <?php Utils::show($story,"summary");?></div>
    <div class="card-text">Author: <?php Utils::show($story,"author");?></div>
    <div>
        <?php if($author_id == Utils::get($story, "author")):?>
            <a class="btn btn-primary"
               href="<?php echo "index.php?arc/create&story=" . Utils::get($story, "id");?>">Create Arc</a>
        <?php endif; ?>
        <a class="btn btn-primary"
            href="<?php echo "index.php?story/view&arc=" . Utils::get($story, "id");?>">Start Reading</a>
    </div>
    </div>
</div>
