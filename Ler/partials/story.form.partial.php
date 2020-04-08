<form method="POST">
    <div>
        <label for="title">Title:</label><input type="text" min="1" name="title" id="title" value="<?php show($story,"title");?>"/>
    </div>
    <div>
        <label for="summary">Summary:</label><textarea min="1" name="summary" id="summary" value="<?php show($story,"summary");?>"></textarea>
    </div>
    <div>
        <?php
            if(!empty($story)){
                $submit_button = "Save";
            }
            else{
                $submit_button = "Create";
            }
        ?>
        <input type="Submit" name="save_story" value="<?php echo $submit_button;?>"/>
    </div>
</form>