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
    $egg = $_POST["egg_id"];
    if ($egg <= 0) {
        $egg = null;
    }
    $br = $_POST["base_rate"];
    $min = $_POST["mod_min"];
    $max = $_POST["mod_max"];
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE F20_Incubators set name=:name, egg_id=:egg, base_rate=:br, mod_min=:min, mod_max=:max where id=:id");
        $r = $stmt->execute([
            ":name" => $name,
            ":egg" => $egg,
            ":br" => $br,
            ":min" => $min,
            ":max" => $max,
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
    $stmt = $db->prepare("SELECT * FROM F20_Incubators where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
//get eggs for dropdown
$db = getDB();
$stmt = $db->prepare("SELECT id,name from F20_Eggs LIMIT 10");
$r = $stmt->execute();
$eggs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Edit Incubator</h3>
    <form method="POST">
        <label>Name</label>
        <input name="name" placeholder="Name" value="<?php echo $result["name"]; ?>"/>
        <label>Egg</label>
        <select name="egg_id" value="<?php echo $result["egg_id"];?>" >
            <option value="-1">None</option>
            <?php foreach ($eggs as $egg): ?>
                <option value="<?php safer_echo($egg["id"]); ?>" <?php echo ($result["egg_id"] == $egg["id"] ? 'selected="selected"' : ''); ?>
                ><?php safer_echo($egg["name"]); ?></option>
            <?php endforeach; ?>
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
