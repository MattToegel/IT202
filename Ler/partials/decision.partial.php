<?php //requires $decision, $arc_id
if(!isset($decision)){
    $decision = array();
}
if(!isset($arc_id)){
    $arc_id = -1;
}
?>
<div data-toggle="fieldset-entry">
    <div class="input-group input-group-sm mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="">Decision</span>
        </div>
        <textarea class="form-control" min="1" name="dcontent[]" id="0" rows="3"
            placeholder="Write a brief action that'll segway to the next arc"><?php
            Utils::show($decision, "content");
            ?></textarea>
        <select name="nextarc[]">
            <option value="-1">Select a target Arc</option>
            <?php if (isset($myarcs)):?>
                <?php foreach($myarcs as $_arc):?>
                    <?php if($_arc["id"] != $arc_id):?>
                        <option
                                <?php if(Utils::get($_arc, "id") == Utils::get($decision,"next_arc_id"))echo"selected";?>
                                value="<?php Utils::show($_arc, "id");?>"><?php
                            echo Utils::show($_arc, "title");
                        ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <button type="button" class="btn btn-danger btn-sm" data-toggle="fieldset-remove-row" id="q-{{loop.index0}}-remove">Delete</button>
    </div>
</div>