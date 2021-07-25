<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
?>
<?php if (isset($_POST["name"])) {
    $name = se($_POST, "name", false, false);
    $starting_reward = (int)se($_POST, "starting_reward", 0, false);
    $cost = $starting_reward + 1;
    $balance = (int)se(get_account_balance(), null, 0, false);
    $entry_fee = (int)se($_POST, "entry_fee", 0, false);
    $reward_increase = (float)se($_POST, "reward_increase", 0, false);
    $min_participants = (int)se($_POST, "min_participants", 3, false);
    $payout_split = se($_POST, "payout", 1, false);
    $duration = (int)se($_POST, "duration", 3, false);
    switch ($payout_split) {
        case "2":
            $payout = ".8,.2";
            break;
        case "3":
            $payout = ".7,.2,.1";
            break;
        case "4":
            $payout = ".6,.3,.1";
            break;
        case "5":
            $payout = ".34,.33,.33";
            break;
        default:
            $payout = "1";
            break;
    }
    $isValid = true;
    //validate
    if ($starting_reward < 0) {
        flash("Invalid Starting Reward", "warning");
        $isValid = false;
    }
    if ($cost < 1) {
        flash("Invalid Cost", "danger");
        $isValid = false;
    }
    if ($cost > $balance) {
        flash("You can't afford this, it requires $cost points", "warning");
        $isValid = false;
    }
    if ($min_participants < 3) {
        flash("All competitions require at least 3 participants to payout", "warning");
    }
    if (!!$name === false) {
        flash("Name must be set", "warning");
        $isValid = false;
    }
    if ($entry_fee < 0) {
        flash("Entry fee must be free (0) or greater", "warning");
        $isValid = false;
    }
    if ($reward_increase < 0.0 || $reward_increase > 1.0) {
        flash("The reward increase can only be between 0% - 100% of the Entry Fee", "warning");
        $isValid = false;
    }
    if ($duration < 3 || is_nan($duration)) {
        flash("Competitions must be 3 or greater days", "warning");
        $isValid = false;
    }
    if ($isValid) {
        //create competition and deduct cost
        $db = getDB();
        //setting 1 for participants since we'll be adding creator to the comp, this saves an update query
        //using sql to calculate the expires date by passing in a sanitized/validated $duration
        //setting starting_reward and current_reward to the same value
        $query = "INSERT INTO Competitions (name, creator, starting_reward, current_reward, min_participants, current_participants, entry_fee, reward_increase, payouts, expires)
        values (:n, :c, :sr,:sr, :mp,1, :ef, :ri, :p, DATE_ADD(NOW(), INTERVAL $duration day))";
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([
                ":n" => $name,
                ":c" => get_user_id(),
                ":sr" => $starting_reward,
                ":mp" => $min_participants,
                ":ef" => $entry_fee,
                ":ri" => $reward_increase,
                ":p" => $payout

            ]);
            $comp_id = (int)$db->lastInsertId();
            if ($comp_id > 0) {
                change_points($cost, "create-comp", get_user_account_id(), -1, "Created Competition #$comp_id");
                //TODO creator joins competition for free
                error_log("Attempt to join created competition: " . join_competition($comp_id, true));
                flash("Successfully created Competition $name", "success");
            }
        } catch (PDOException $e) {
            error_log("Error creating competition: " . var_export($e->errorInfo, true));
            flash("There was an error creating the competition: " . var_export($e->errorInfo[2]), "danger");
        }
    }
}
?>
<div class="container-fluid">
    <?php $title = "Create Competition";
    include(__DIR__ . "/../../partials/title.php"); ?>
    <form method="POST" autocomplete="off">
        <div>
            <label class="form-label" for="name">Name/Title</label>
            <input class="form-control" type="text" name="name" id="name" required />
        </div>
        <div>
            <label class="form-label" for="sr">Starting Reward</label>
            <input class="form-control" type="number" name="starting_reward" id="sr" min="1" value="1" oninput="document.getElementById('cost').innerText = 1 + (value*1)" required />
        </div>
        <div>
            <label class="form-label" for="ef">Entry Fee</label>
            <input class="form-control" type="number" name="entry_fee" id="ef" min="0" value="0" required />
        </div>
        <div>
            <label class="form-label" for="ri">Reward Increase (<span id="riv">0</span>%)</label>
            <input class="form-control" type="range" name="reward_increase" value="0" oninput="document.getElementById('riv').innerText = (value*100)" step="0.1" id="ri" min="0.0" max="1.0" required />
        </div>
        <div>
            <label class="form-label" for="rp">Min. Required Participants</label>
            <input class="form-control" type="number" name="min_participants" id="rp" min="3" value="3" required />
        </div>
        <div>
            <label class="form-label" for="d">Duration in Days</label>
            <input class="form-control" type="number" name="duration" id="d" min="3" value="3" required />
        </div>
        <div>
            <label class="form-label" for="payout">Payout Split</label>
            <select class="form-control" name="payout" required>
                <option value="1">100% to First</option>
                <option value="2">80% to First, 20% to Second</option>
                <option value="3">70% to First, 20% to Second, 10% to Third</option>
                <option value="4">60% to First, 30% to Second, 10% to Third</option>
                <option value="5">34% to First, 33% to Second, 33% to Third</option>
            </select>
        </div>
        <div>Cost: <span id="cost">2</span></div>
        <input class="btn btn-primary" type="submit" value="Create" />
    </form>
</div>
<script>
    function validate(form) {
        //TODO add all validations (basically match what you define at the html level for consistency)

        //client side balance validation (just used to reduce server load as we don't trust the client)
        let balance = <?php se(get_account_balance(), null, 0); ?> * 1; //convert to int
        let cost = 1 + (form.starting_reward.value * 1);
        if (cost < 1) {
            cost = 1;
        }
        let isValid = true;
        if (cost > balance) {
            flash("You can't afford to create this competition, you need " + cost + " points");
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>