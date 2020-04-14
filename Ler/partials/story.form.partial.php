<form method="POST">
    <div class="form-group">
        <label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title" value="<?php Utils::show($story,"title");?>"/>
    </div>
    <div class="form-group">
        <label for="summary">Summary:</label>
        <textarea class="form-control" min="1" name="summary" id="summary" value="<?php Utils::show($story,"summary");?>"></textarea>
    </div>
    <div class="">
        <?php
            if(!empty($story)){
                $submit_button = "Save";
            }
            else{
                $submit_button = "Create";
            }
        ?>
        <input class="btn btn-primary" type="Submit" name="save_story" value="<?php echo $submit_button;?>"/>
    </div>
</form>