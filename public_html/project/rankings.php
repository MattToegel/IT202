<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    $result = DBH::get_n_competitions_ending_soonest(3);
    $comps = [];
    if(Common::get($result, "status", 400) == 200){
        $comps = Common::get($result, "data", []);
    }
}
?>
<div class="container-fluid">
<h4>Top 3 Competitions</h4>
<div class="list-group">
<?php if(isset($comps) && count($comps) > 0):?>
    <?php foreach($comps as $c):?>

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
            <div class="row">
                <div class="col-4">
                    <div><?php echo Common::get($c, "title");?></div>
                    <div>Reward: <?php echo Common::get($c, "points", 1);?></div>
                </div>
                <div class="col-4">
                    <div>Expires: <?php echo Common::get($c, "expires");?></div>
                </div>
                <div class="col-4">
                    <div>Participants: <?php echo $participants;?></div>
                </div>
            </div>
            <div class="row">
                <?php $comp_id = Common::get($c, "id", -1);?>
                <?php include(__DIR__."/partials/scoreboard.partial.php");?>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <div class="list-group-item">No competitions available</div>
<?php endif;?>
</div>
</div>