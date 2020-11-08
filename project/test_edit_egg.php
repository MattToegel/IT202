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
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $name = $_POST["name"];
    $state = $_POST["state"];
    $br = $_POST["base_rate"];
    $min = $_POST["mod_min"];
    $max = $_POST["mod_max"];
    $nst = date('Y-m-d H:i:s');//calc
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE F20_Eggs set name=:name, state=:state, base_rate=:br, mod_min=:min, mod_max=:max, next_stage_time=:nst where id=:id");
        //$stmt = $db->prepare("INSERT INTO F20_Eggs (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
        $r = $stmt->execute([
            ":name" => $name,
            ":state" => $state,
            ":br" => $br,
            ":min" => $min,
            ":max" => $max,
            ":nst" => $nst,
            ":id" => $id
        ]);
        if ($r) {
            flash("Updated successfully with id: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM F20_Eggs where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
    <h3>Edit Egg</h3>
    <form method="POST">
        <label>Name</label>
        <input name="name" placeholder="Name" value="<?php echo $result["name"]; ?>"/>
        <label>State</label>
        <select name="state" value="<?php echo $result["state"]; ?>">
            <option value="0" <?php echo($result["state"] == "0" ? 'selected="selected"' : ''); ?>>Stasis</option>
            <option value="1" <?php echo($result["state"] == "1" ? 'selected="selected"' : ''); ?>>Incubating</option>
            <option value="2" <?php echo($result["state"] == "2" ? 'selected="selected"' : ''); ?>>Hatching</option>
            <option value="3" <?php echo($result["state"] == "3" ? 'selected="selected"' : ''); ?>>Hatched</option>
            <option value="4" <?php echo($result["state"] == "4" ? 'selected="selected"' : ''); ?>>Expired</option>
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
