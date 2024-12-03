<?php if (isset($data)) : ?>
    <?php
    $_value = se($data, "value", -1, false);
    $_max = se($data, "max", 5, false);
    ?>
    <span class="stars">
        <?php if ($_value < 0) : ?>
            N/A
        <?php else : ?>
            <?php for ($i = 0; $i < $_max; $i++) : ?>
                <?php if ($i < $_value) : ?>
                    <i class="bi bi-star-fill"></i>
                <?php else : ?>
                    <i class="bi bi-star"></i>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
    </span>
<?php endif; ?>