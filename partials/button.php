<?php if (isset($data)) : ?>
    <?php
    //setup some variables for readability
    $_btn_type = se($data, "type", "button", false);
    $_btn_text = se($data, "text", "Button", false);
    $_btn_color = se($data, "color", "primary", false);
    //TODO add support for onClick
    $_onclick = isset($data["onclick"]) ? $data["onclick"] : ""; //important be very cautious as this can allow XSS attacks
    //can't use se() here as it'll convert any characters needed for the function call.
    $_extras = isset($data["extras"]) ? $data["extras"] : "";
    if (!empty($_extras)) {
        //map rules to key="value"
        $_extras = array_map(function ($key, $value) {
            //used to convert html attributes that don't require a value like required, disabled, readonly, etc
            if ($value === true) {
                return $key;
            }
            return $key . '="' . $value . '"';
        }, array_keys($_extras), $_extras);
        //convert array to a space separate string
        $_extras = implode(" ", $_extras);
    }
    ?>
    <?php if ($_btn_type === "button") : ?>
        <button type="button" class="btn btn-<?php se($_btn_color); ?>" onclick="<?php echo $_onclick; ?>" <?php echo $_extras; ?>> <?php se($_btn_text); ?></button>
    <?php elseif ($_btn_type === "submit") : ?>
        <input type="submit" class="btn btn-<?php se($_btn_color); ?>" value="<?php se($_btn_text); ?>" onclick="<?php echo $_onclick; ?>" <?php echo $_extras; ?> />
    <?php endif; ?>

    <?php
    //cleanup just in case this is used directly instead of via render_button()
    // if it's used from the function, the variables will be out of scope when the function is done so there'd be no need to unset them
    unset($_btn_type);
    unset($_btn_btn_text_type);
    unset($_btn_color);
    ?>
<?php endif; ?>