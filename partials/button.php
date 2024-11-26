<?php if (isset($data)) : ?>
    <?php
    //setup some variables for readability
    $_btn_type = se($data, "type", "button", false);
    $_btn_text = se($data, "text", "Button", false);
    $_btn_color = se($data, "color", "primary", false);
    //TODO add support for onClick
    unset($data["type"]);
    unset($data["text"]);
    unset($data["color"]);
    // NOTE, this can lead to XSS and other attacks, you'll want to ensure content in the rest of the $data
    // arry is trusted (i.e., use htmlspecialchars/htmlentities/etc to sanitize)
    $other_props = "";
    foreach($data as $key=>$value){
        $other_props .= "$key=\"$value\"";
    }
    ?>
    <?php if ($_btn_type === "button") : ?>
        <button class="btn btn-<?php se($_btn_color); ?>" <?php echo $other_props?>><?php se($_btn_text); ?></button>
    <?php elseif ($_btn_type === "submit") : ?>
        <input type="submit" class="btn btn-<?php se($_btn_color); ?>" value="<?php se($_btn_text); ?>" <?php echo $other_props?> />
    <?php elseif ($_btn_type === "reset") : ?>
        <input type="reset" class="btn btn-<?php se($_btn_color); ?>" value="<?php se($_btn_text); ?>" <?php echo $other_props?> />
    <?php endif; ?>

    <?php
    //cleanup just in case this is used directly instead of via render_button()
    // if it's used from the function, the variables will be out of scope when the function is done so there'd be no need to unset them
    unset($_btn_type);
    unset($_btn_btn_text_type);
    unset($_btn_color);
    ?>
<?php endif; ?>