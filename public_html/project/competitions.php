<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    $results = DBH::get_competitions();
}
?>
<h4>Competitions</h4>
<div class="list-group">
<?php if(isset($results) && count($results) > 0):?>
    <?php foreach($results as $c):?>
        <?php
        $fee = Common::get($c, "entry_fee", 0);
        if ($fee < 1){
            $feeText = "free";
        }
        else{
            $feeText = $fee;
        }
        $participants = ''.join([
                Common::get($c, "participants", 0),
                "/",
                Common::get($c, "min_participants", 3)
            ]);
        ?>
        <div class="list-group-item">
            <div>
                <div><?php echo Common::get($c, "title");?></div>
                <div>Reward: <?php echo Common::get($c, "points", 1);?></div>
            </div>
            <div>
                <div>Duration: <?php echo Common::get($c, "duration", 1);?> Day(s)</div>
                <div>Expires: <?php echo Common::get($c, "expires");?></div>
            </div>
            <div>
                <div>Entry Fee: <?php echo $feeText;?></div>
                <div>Participants: <?php echo $participants;?></div>
            </div>
            <div>
                <a class="btn btn-primary" href="#/<?php echo $fee;?>">Join (<?php echo $feeText;?>)</a>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <div class="list-group-item">No competitions available</div>
<?php endif;?>
</div>
