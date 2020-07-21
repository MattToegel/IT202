<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::get($_GET, "c", false)){
    $comp_id = (int)Common::get($_GET, "c", -1);
    //find comp_id=>price from $_SESSION["competitions"]
    echo var_export($_SESSION["competitions"], true);
    $cost = Common::get($_SESSION["competitions"], $comp_id, -1);
    if($cost < 0){
        error_log("Error fetching cost");
    }
    else {
        //$cost = (int)Common::get($_GET, "cost", 0);
        $available = Common::get($_SESSION["user"], "points", 0);
        if ($cost <= $available) {


            //immediately update session to prevent a needed db call
            //$available -= $cost;
            //$_SESSION["user"]["points"] = $available;
        } else {
            Common::flash("You can't afford to join this competition, come back later", "warning");
        }
    }
    //don't continue the page, redirect to drop the $_GET data after it's processed
    die(header("Location: competitions.php"));
}
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    $results = DBH::get_competitions();
    $comps = [];
    if(Common::get($results, "status", 400) == 200){
        $comps = Common::get($results, "data", []);
        //stuff to reduce DB calls
        unset($_SESSION["competitions"]);
        foreach($comps as $c){
            array_push($_SESSION["competitions"], [(int)$c["id"]=>$c["entry_fee"]]);
        }
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
