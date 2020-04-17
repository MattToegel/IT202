<div class="card">
    <div class="card-body">
    <h5 class="bg-primary"><?php Utils::show($story, "title");?></h5>
    <div class="card-text">Summary: <?php Utils::show($story,"summary");?></div>
    <div class="card-text">Author: <?php Utils::show($story,"author");?></div>
    <div><a href="<?php echo "index.php?arc/create&story=" . Utils::get($story, "id");?>">Create Arc</a></div>
    </div>
</div>
