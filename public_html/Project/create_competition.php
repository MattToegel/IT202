<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$payout_options = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, CONCAT(first_place,'% - ', second_place, '% - ', third_place, '%') as place FROM BGD_Payout_Options");
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $payout_options = $r;
    }
} catch (PDOException $e) {
    flash("There was a problem fetching first, second, third place options", "danger");
    error_log("Error Getting Places: " . var_export($e, true));
}
//save
if (isset($_POST["title"]) && !empty($_POST["title"])) {
    $cost = (int)se($_POST, "starting_reward", 0, false);
    $cost++;
    $cost += (int)se($_POST, "join_cost", 0, false);
    $title = se($_POST, "title", "N/A", false);
    $balance = get_account_balance();
    if ($balance >= $cost) {
        $db->beginTransaction();
        if (change_bills($cost, "create_comp", get_user_account_id(), -1, "Create Competition $title")) {
            $_POST["creator_id"] = get_user_id();
            $comp_id = save_data("BGD_Competitions", $_POST);
            if ($comp_id > 0) {
                if (add_to_competition($comp_id, get_user_id())) {
                    flash("Successfully created competition", "success");
                    $db->commit();
                } else {
                    $db->rollback();
                }
            } else {
                $db->rollback();
            }
        } else {
            flash("There was a problem deducting points", "warning");
            $db->rollback();
        }
    } else {
        flash("You can't afford this right now", "warning");
    }
}
?>

<div class="container-fluid">
    <h1>Create Competition</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input id="title" name="title" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="reward" class="form-label">Starting Reward</label>
            <input id="reward" type="number" name="starting_reward" class="form-control" onchange="updateCost()" placeholder=">= 1" min="1" />
        </div>
        <div class="mb-3">
            <label for="ms" class="form-label">Min. Score</label>
            <input id="ms" name="min_score" type="number" class="form-control" placeholder=">= 1" min="1" />
        </div>
        <div class="mb-3">
            <label for="mp" class="form-label">Min. Participants</label>
            <input id="mp" name="min_participants" type="number" class="form-control" placeholder=">= 3" min="3" />
        </div>
        <div class="mb-3">
            <label for="jc" class="form-label">Join Cost</label>
            <input id="jc" name="join_cost" type="number" class="form-control" onchange="updateCost()" placeholder=">= 0" min="0" />
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration (in Days)</label>
            <input id="duration" name="duration" type="number" class="form-control" placeholder=">= 3" min="3" />
        </div>
        <div class="mb-3">
            <label for="po" class="form-label">Payout Option</label>
            <select id="po" name="payout_option" class="form-control">
                <?php foreach ($payout_options as $po) : ?>
                    <option value="<?php se($po, 'id'); ?>"><?php se($po, 'place'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <input type="submit" value="Create Competition (Cost: 2)" class="btn btn-primary" />
        </div>
    </form>
    <script>
        function updateCost() {
            let starting = parseInt(document.getElementById("reward").value || 0) + 1;
            let join = parseInt(document.getElementById("jc").value || 0);
            if (join < 0) {
                join = 1;
            }
            let cost = starting + join;
            document.querySelector("[type=submit]").value = `Create Competition (Cost: ${cost})`;
        }
    </script>
</div>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>