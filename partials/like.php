<?php if (isset($data)) : ?>
    <?php
    $_value = se($data, "value", 0, false);
    $_icon_fill = "heart-fill";
    $_icon_empty = "heart";
    $_type = se($data, "type", "heart", false);
    switch ($_type) {
        case "star":
            $_icon_empty = "star";
            $_icon_fill = "star-fill";
            break;
        case "check":
            $_icon_empty = "patch-check";
            $_icon_fill = "patch-check-fill";
            break;
        case "toggle":
            $_icon_empty = "toggle-off";
            $_icon_fill = "toggle-on";
            break;
    }
    ?>
    <span class="like">
        <?php if ($_value > 0) : ?>
            <i class="bi bi-<?php se($_icon_fill); ?>"></i>
        <?php else : ?>
            <i class="bi bi-<?php se($_icon_empty); ?>"></i>
        <?php endif; ?>
    </span>
<?php endif; ?>