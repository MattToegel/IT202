
<?php

if(!isset($id)){
    $id = uniqid();
}
if(!isset($name)){
    flash("name must be set for the form", "danger");
}
if(!isset($value)){
    $value = "";
}
if(!isset($label)){
    $label = "I'm not labeled";
}
if(!isset($type)){
    $type = "text";
}
?>
<div class="mb-3">
    <label class="form-label text-info" for="<?php se($id);?>"><?php se($label);?></label>
    <input type="<?php se($type);?>" class="form-control" value="<?php se($value);?>"/>
</div>