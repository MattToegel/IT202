<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM F20_Eggs where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <?php safer_echo($result["name"]); ?>
        </div>
        <div class="card-body">
            <div>
                <p>Stats</p>
                <div>Rate: <?php safer_echo($result["base_rate"]); ?></div>
                <div>Modifier: <?php safer_echo($result["mod_min"]); ?> - <?php safer_echo($result["mod_max"]); ?></div>
                <div>Current State: <?php getState($result["state"]); ?></div>
                <div>Next Stage: <?php safer_echo($result["next_stage_time"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input name="name" placeholder="Name" value="<?php echo $result["name"]; ?>"/>
        <label>State</label>
        <select name="state" value="<?php echo $result["state"]; ?>">
            <option value="0" <?php echo($result["state"] == "0" ? 'selected="selected"' : ''); ?>>Incubating</option>
            <option value="1" <?php echo($result["state"] == "1" ? 'selected="selected"' : ''); ?>>Hatching</option>
            <option value="2" <?php echo($result["state"] == "2" ? 'selected="selected"' : ''); ?>>Hatched</option>
            <option value="3" <?php echo($result["state"] == "3" ? 'selected="selected"' : ''); ?>>Expired</option>
        </select>
        <label>Base Rate</label>
        <input type="number" min="1" name="base_rate" value="<?php echo $result["base_rate"]; ?>"/>
        <label>Mod Min</label>
        <input type="number" min="1" name="mod_min" value="<?php echo $result["mod_min"]; ?>"/>
        <label>Mod Max</label>
        <input type="number" min="1" name="mod_max" value="<?php echo $result["mod_max"]; ?>"/>
        <input type="submit" name="save" value="Update"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php");
