<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to waste...spend your points");
    die(header("Location: login.php"));
}
?>
<?php

if (isset($_POST["submit"])) {
    $title = safe_get($_POST, "title", "");
    $user_id = get_user_id();
    $duration = (int)(safe_get($_POST, "duration", 1));
    $newDate = new DateTime();
    $newDate->add(new DateInterval("P".$duration."D")); // P1D means a period of 1 day
    $expires = $newDate->format('Y-m-d H:i:s'); //<- mysql date format
    $fpp = (float)safe_get($_POST, "fpp", 0);
    $spp = (float)safe_get($_POST, "spp", 0);
    $tpp = (float)safe_get($_POST, "tpp", 0);
    $entryFee = (int)safe_get($_POST, "entryFee", 0);
    $feePercent = (float)safe_get($_POST, "feePercent", 0);
    $isValid = true;
    if ((int)round($fpp * 100) + (int)round($spp * 100) + (int)round($tpp * 100) !== 100) {
        flash("1st, 2nd, and 3rd MUST total to 100%");
        $isValid = false;
    }
    if ($duration < 1) {
        flash("Duration must be at least 1 day");
        $isValid = false;
    }
    if ($entryFee < 0) {
        flash("Entry fee must be 0 (for free) or greater");
        $isValid = false;
    }
    if ($feePercent < 0 || $feePercent > 1) {
        flash("Percentage of the entry fee must be between 0% and 100%");
        $isValid = false;
    }
    $incrementOnEntry = $feePercent > 0; //increment if there's a percentage of the fee taken
    $points = (int)safe_get($_POST, "points", 1);
    //TODO continue points validation
    if ($points < 0) {
        flash("Points must not be less than 0");
        $isValid = false;
    }
    $minParticipants = (int)safe_get($_POST, "minParticipants", 3);
    if ($minParticipants < 3) {
        flash("Minimum participants must be 3 or greater");
        $isValid = false;
    }
    $cost = $points + 1;
    if ($cost < 1) {
        flash("Invalid cost, please try again");
        $isValid = false;
    }
    $balance = get_points_balance();
    if($balance <= 0 || $cost > $balance){
        flash("You don't have enough points");
        $isValid = false;
    }
    if ($isValid) {
        $query = "INSERT INTO tfp_competitions (title, user_id, duration, expires, first_place, second_place, third_place, entry_fee, increment_on_entry, percent_of_entry, points, min_participants)";
        $query .= " VALUES(:t, :uid, :d, :e, :fp, :sp, :tp, :ef, :ioe, :poe, :p, :mp)";
        $params = [
            ":t"=>$title,
            ":uid"=>$user_id,
            ":d"=>$duration,
            ":e"=>$expires,
            ":fp"=>$fpp,
            ":sp"=>$spp,
            ":tp"=>$tpp,
            ":ef"=>$entryFee,
            ":ioe"=>$incrementOnEntry?1:0,
            ":poe"=>$feePercent,
            ":p"=>$points,
            ":mp"=>$minParticipants
        ];
        $db = getDB();
        $stmt = $db->prepare($query);
        $r = $stmt->execute($params);
        if($r){
            changePoints($user_id, -$cost, "created competition: " . $db->lastInsertId);
            flash("Competition created successfully");
            die(header("Location: #"));
            //TODO add creator to competition automatically
        }
        else{
            flash("Error creating competion: " . var_export($stmt->errorInfo(), true));
        }
    }
}
?>
<div class="container">
    <div class="h3">Create Competition Form</div>
    <form method="POST" onsubmit="return isValid(this);">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input id="title" name="title" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="dur" class="form-label">Duration (in days)</label>
            <input id="dur" name="duration" class="form-control" type="number" min="1" value="3" required />
        </div>
        <div class="mb-3">
            <label for="ef" class="form-label">Entry Fee <small>(Free will ignore increment on entry)</small></label>
            <input id="ef" name="entryFee" class="form-control" type="number" min="0" value="1" required />
        </div>
        <div class="mb-3">
            <label for="pf" class="form-label">Percent of Fee for Reward Increment <span id="pfv">0%</span></label>
            <input id="pf" name="feePercent" class="form-range" type="range" min="0" max="1" step="0.05" value="0" oninput="updateRangeView('pfv',this.value, '%');" required />
        </div>
        <div class="mb-3">
            <label for="reward" class="form-label">Reward Payout Start</label>
            <input id="reward" name="reward" class="form-control" type="number" min="1" value="1" required oninput="updateCost(this);" />
        </div>
        <div class="mb-3">
            <label for="mp" class="form-label">Min. Required Participants</label>
            <input id="mp" name="minParticipants" class="form-control" type="number" min="3" value="3" required />
        </div>
        <div class="input-group">
            <span class="input-group-text">Payout Percentages: 1st (<span id="fppv">70%</span>), 2nd (<span id="sppv">20%</span>), 3rd (<span id="tppv">10%</span>)</span>
            <input name="fpp" class="form-range payout" type="range" min="0" max="1" step="0.05" value=".7" oninput="updateRangeView('fppv',this.value, '%');" required />
            <input name="spp" class="form-range payout" type="range" min="0" max="1" step="0.05" value=".2" oninput="updateRangeView('sppv',this.value, '%');" required />
            <input name="tpp" class="form-range payout" type="range" min="0" max="1" step="0.05" value=".1" oninput="updateRangeView('tppv',this.value, '%');" required />
        </div>
        <div class="d-grid gap-2 mt-3">
            <input type="submit" name="submit" class="btn btn-success" id="submit" value="Create (Points: 1)" />
        </div>
    </form>
</div>
<script>
    function updateCost(ele) {
        document.getElementById("submit").value = "Create (Points: " + (Math.round(ele.value)+1) + ")";
    }

    function updateRangeView(id, value, suffix) {
        document.getElementById(id).innerText = (Math.round(value * 100)) + suffix;
    }

    function constrainPayoutPercentages() {
        let $payouts = $(".payout");
        let sum = 0;
        $payouts.each((index, ele) => {
            sum += Math.round(parseFloat(parseFloat($(ele).val()).toPrecision(2)) * 100);
        });
        console.log("sum", sum);
        return sum;
    }

    function isValid(form) {
        let sum = constrainPayoutPercentages();
        if (sum !== 100) {
            alert("Payout percentages must equal 100%");
            return false;
        }
    }
    $(document).ready(()=>{
        updateCost(document.getElementById("reward"));
    })
</script>
<?php require(__DIR__ . "/partials/flash.php");
