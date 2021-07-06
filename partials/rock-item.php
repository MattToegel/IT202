<?php
//requires $rock to be present
if (!isset($rock)) {
    $rock = [];
}
//requires a flag to be passed in one use-case
if (!isset($isPotential)) {
    $isPotential = false;
}
?>
<div class="card">
    <div class="card-body">
        <div class="card-title">
            Rock #<?php se($rock, "id", 0); ?>
        </div>
        <div class="card-text">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    Days to mine: <?php se($rock, "time_to_mine", 9999); ?>
                </li>
                <li class="list-group-item">
                    Reward: <?php echo ((float)se($rock, "percent_chance", 0, false) * 100); ?>%
                    chance for <?php se($rock, "potential_reward", 0); ?>
                </li>
                <?php if (se($rock, "opens_date", null, false) !== null) : ?>
                    <li>
                        Opens: <?php se($rock, "opens_date"); ?>
                    </li>
                <?php endif; ?>
            </ul>
            <?php if ($isPotential) : ?>
                <button type="button" class="btn btn-primary" id="<?php se($rock, "id", -1); ?>">Choose this one</button>
            <?php else : ?>
                <?php if (se($rock, "is_mining", 0, false) === 0) : ?>
                    <button type="button" class="btn btn-primary" id="<?php se($rock, "id", -1); ?>">Start Mining</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>