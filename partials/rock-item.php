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
<div class="card h-100" style="width:20em">
    <div class="card-body">
        <div class="card-title">
            Rock #<?php se($rock, "id", 0); ?>
        </div>
        <div class="justify-content-center" style="text-align:center">
            <?php if (!!se($rock, "is_mining", 0, false) === false) : ?>
                <img style="height:5em" src="images/default.svg" class="img-fluid" alt="not mining" />
            <?php else : ?>
                <img style="height:5em" src="images/mining.svg" class="img-fluid" alt="mining" />
            <?php endif; ?>
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

                <?php if (!!se($rock, "opens_date", null, false) === true) : ?>
                    <li class="list-group-item">
                        Opens: <?php se($rock, "opens_date"); ?><br>
                        <small id="cd_<?php se($rock, "id", -1);?>">
                        </small>
                        <script>
                            setInterval(()=>{
                                let d = new Date("<?php se($rock, "opens_date");?>");
                                let m = diff_ms(new Date(), d);
                                if(d >= new Date()){
                                    document.getElementById("cd_<?php se($rock, "id");?>").innerText = formatDuration(m);
                                }
                            }, 1000);
                            </script>
                    </li>
                <?php endif; ?>
            </ul>
            <?php if ($isPotential) : ?>
                <button type="button" onclick="pickRock(this)" class="btn btn-primary" id="<?php se($rock, "id", -1); ?>">Choose this one</button>
            <?php else : ?>
                <?php if (!!se($rock, "is_mining", 0, false) === false) : ?>
                    <button type="button" class="btn btn-primary" onclick="prepareMining(this)" id="<?php se($rock, "id", -1); ?>">Start Mining</button>
                <?php endif; ?>
                <?php if (!!se($rock, "opens_date", "", false) === true) :   ?>
                    <?php if (date('Y-m-d H:i:s', strtotime(se($rock, "opens_date", "", false))) <= date("Y-m-d  H:i:s")) :   ?>
                        <button type="button" onclick="checkReward(this)" class="btn btn-success" id="<?php se($rock, "id", -1); ?>">Check Reward</button>
                    <?php endif;   ?>
                <?php endif;   ?>
            <?php endif; ?>
        </div>
    </div>
</div>