<form method="POST">
    <div class="form-group">
        <label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title" value="<?php Utils::show($story,"title");?>"/>
    </div>
    <div class="form-group">
        <label for="summary">Summary:</label>
        <textarea class="form-control" min="1" name="summary" id="summary"><?php
            //by placing the opening and closing tags immediately after and before the textarea tags
            //it prevents "mysterious" whitespace from showing up in our prefill
            //anything between the textarea tags is read as a default value including whitespace
            Utils::show($story,"summary");
        ?></textarea>
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