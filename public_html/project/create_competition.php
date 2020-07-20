<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    if(!Common::has_role("Admin")){
        die(header("Location: home.php"));
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div class="container-fluid">
    <form method="POST">
        <div class="form-group">
            <label for="competition_name">Competition Name</label>
            <input class="form-control" type="text" id="competition_name" name="competition_name" required/>
        </div>
        <div class="form-group">
            <label for="duration">Duration (in days)</label>
            <input class="form-control" type="number" id="duration" name="duration" min="1" required/>
        </div>
        <div class="form-group">
            <label for="fpp">First Place %</label>
            <input class="form-control" type="range" id="fpp" name="fpp" value="1" min="0" max="100"/>
        </div>
        <div class="form-group">
            <label for="spp">Second Place %</label>
            <input class="form-control" type="range" id="spp" name="spp" value="0" min="0" max="100"/>
        </div>
        <div class="form-group">
            <label for="tpp">Third Place %</label>
            <input class="form-control" type="range" id="tpp" name="tpp" value="0" min="0" max="100"/>
        </div>
        <div class="form-group">
            <label for="entry_fee">Entry Fee (points)</label>
            <input class="form-control" type="number" id="entry_fee" name="entry_fee" value="0" min="0"/>
        </div>
        <div class="form-group">
            <label for="increment_entry">Increment On Entry</label>
            <input class="form-control" type="checkbox" onchange="toggleEntryIncrement(this);"
                   id="increment_entry" name="increment_entry"/>
        </div>
        <div class="form-group eci" style="display:none;">
            <label for="eci">Entry Cost Increment %</label>
            <input class="form-control" type="range" id="eci" name="eci" value="0" min="0" max="100"/>
        </div>
        <div class="form-group">
            <label for="min_participants">Minimum Participants for Reward</label>
            <input class="form-control" type="number" id="min_participants" name="min_participants" value="3" min="3" required/>
        </div>
        <div class="form-group">
            <label for="reward">Points Reward</label>
            <input class="form-control" type="number" onchange="updateCost(this);"
                   id="reward" name="reward" value="1" min="1" max="50" required/>
        </div>
        <div class="form-group">
            <input type="submit" id="submit" name="submit" class="btn btn-primary" value="Create Competition (Cost: 1)"/>
        </div>
    </form>
    <?php
    if(Common::get($_POST, "submit", false)){
        $points = Common::clamp(Common::get($_POST,"reward", 1), 1, 50);
        $available = Common::get($_SESSION["user"], "points", 0);
        if(($points+1) <= $available) {
            $result = DBH::changePoints(Common::get_user_id(), -($points+1), -1, "create_competition", "created competition");
            if(Common::get($result, "status", 400) == 200) {
                $competition = [
                    "title" => Common::get($_POST, "competition_name", false),
                    "duration" => Common::clamp(Common::get($_POST, "duration", 1), 1, 365),
                    "first_place" => round(Common::clamp(Common::get($_POST, "fpp", 1.0), 0.1, 1.0), 1),
                    "second_place" => round(Common::clamp(Common::get($_POST, "spp", 0.0), 0.0, 1.0), 1),
                    "third_place" => round(Common::clamp(Common::get($_POST, "tpp", 1.0), 0.0, 1.0), 1),
                    "entry_fee" => Common::clamp(Common::get($_POST, "entry_fee", 0), 0, 10000),
                    "increment_on_entry" => Common::get($_POST, "increment_entry", false) ? 1 : 0,
                    "percent_on_entry" => round(Common::clamp(Common::get($_POST, "eci", 0.0), 0.0, 1.0), 1),
                    "min_participants" => Common::clamp(Common::get($_POST, "min_participants", 3), 3, 10000),
                    "points" => $points
                ];
                //calculate expires date
                $date = new DateTime();
                $interval = new DateInterval('' . join(['P', $competition["duration"], 'D']));
                $date->add($interval);
                $competition["expires"] = $date->format("Y-m-d");
                $competition["user_id"] = Common::get_user_id();

                $fp = $competition["first_place"];
                $sp = $competition["second_place"];
                $tp = $competition["third_place"];
                if (round($fp + $sp + $tp, 1) <= round(1.0, 1)) {
                    //ok
                    $result = DBH::create_competition($competition);
                    if(Common::get($result, "status", 400) == 200){
                        $available -= ($points+1);
                        $_SESSION["user"]["points"] = $available;
                        Common::flash("Created Competition", "success");
                    }
                    else{
                        $result = DBH::changePoints(Common::get_user_id(), ($points+1), -1, "refund", "error creating competition");
                        Common::flash("Error creating Competition", "danger");
                    }
                }
                Common::flash("First, Second, and Third place percentages must be less than or equal to 1", "warning");
            }
            else{
                Common::flash("Error deducting points. Create Competition Canceled", "warning");
            }
        }
        else{
            Common::flash("You can't afford to create a competition right now", "warning");
        }
    }
    ?>
    <script>
        function toggleEntryIncrement(ele){
            if(ele.checked){
                $(".eci").show();
            }
            else{
                $(".eci").hide();
            }
        }
        function updateCost(ele){
            let cost = parseInt($(ele).val()) + 1;
            $("#submit").val("Create Competition (" + cost + ")");
        }
    </script>
</div>
