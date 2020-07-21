<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::get($_GET, "c", false)){
    $comp_id = (int)Common::get($_GET, "c", -1);
    //find comp_id=>price from $_SESSION["competitions"]
    $result = DBH::get_competition_by_id($comp_id);
    if(Common::get($result, "status", 400) == 200) {
        $comp = Common::get($result, "data", []);
        $cost = (int)Common::get($comp, "entry_fee", -1);
        if ($cost < 0) {
            error_log("Error fetching cost");
            Common::flash("Error getting cost of entry", "danger");
        } else {
            //$cost = (int)Common::get($_GET, "cost", 0);
            $available = Common::get($_SESSION["user"], "points", 0);
            if ($cost <= $available) {
                $ioe = Common::get($comp, "increment_on_entry", false)?true:false;
                $poe = Common::get($comp, "percent_of_entry", 0);
                $points = (int)Common::get($comp, "points", 1);
                $isOk = true;
                if($cost > 0){
                    $purchase_result = DBH::changePoints(Common::get_user_id(), -$cost, -1, "join_comp", "joined competition with fee");
                    if(Common::get($purchase_result, "status", 400) == 200){

                    }
                    else{
                        $isOk = false;
                        Common::flash("Error paying competition fee", "danger");
                    }
                }
                if($isOk) {
                    $join_result = DBH::join_competition(Common::get_user_id(), $comp_id);
                    if (Common::get($join_result, "status", 400) == 200) {
                        $stat_result = DBH::get_competition_stats($comp_id);
                        if (Common::get($stat_result, "status", 400) == 200) {
                            $data = Common::get($stat_result, "data", []);
                            $participant_count = Common::get($data, "participants", 1);
                            if ($ioe) {
                                $points = (int)round(($cost * $participant_count) * $poe, 0);
                            }
                            DBH::update_competition_data($comp_id, $points, $participant_count);
                            //immediately update session to prevent a needed db call
                            $available -= $cost;
                            $_SESSION["user"]["points"] = $available;
                        }
                    } else {
                        Common::flash("Error registering for competition, likely you already registered", "warning");
                    }
                }
            } else {
                Common::flash("You can't afford to join this competition, come back later", "warning");
            }
        }
    }
    else{
        Common::flash("Error looking up competition", "danger");
    }
    //don't continue the page, redirect to drop the $_GET data after it's processed
    die(header("Location: competitions.php"));
}
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    $status = Common::get($_GET, "status", false)?true:false;
    $results = DBH::get_competitions($status);
    $comps = [];
    if(Common::get($results, "status", 400) == 200){
        $comps = Common::get($results, "data", []);
    }
}
?>
<h4>Competitions</h4>
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
                <?php
                $params = ''.join([
                        "c=",
                        Common::get($c,"id", -1),
                        "&cost=",
                        $fee
                    ]);
                ?>
                <a class="btn btn-primary" href="competitions.php?<?php echo $params;?>">Join (<?php echo $feeText;?>)</a>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <div class="list-group-item">No competitions available</div>
<?php endif;?>
</div>
