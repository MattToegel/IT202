<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
    <div class="container-fluid">
        <h3>Create Incubator</h3>
        <form method="POST">
            <div class="form-group">
                <label>Name</label>
                <input class="form-control" name="name" placeholder="Name"/>
            </div>
            <div class="form-group">
                <label>Base Rate</label>
                <input class="form-control" type="number" min="1" name="base_rate"/>
            </div>
            <div class="form-group">
                <label>Mod Min</label>
                <input class="form-control" type="number" min="1" name="mod_min"/>
            </div>
            <div class="form-group">
                <label>Mod Max</label>
                <input class="form-control" type="number" min="1" name="mod_max"/>
            </div>
            <input class="btn btn-primary" type="submit" name="save" value="Create"/>
        </form>
    </div>
<?php
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $name = $_POST["name"];
    $br = $_POST["base_rate"];
    $min = $_POST["mod_min"];
    $max = $_POST["mod_max"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO F20_Incubators (name, base_rate, mod_min, mod_max, user_id) VALUES(:name, :br, :min,:max,:user)");
    $r = $stmt->execute([
        ":name" => $name,
        ":br" => $br,
        ":min" => $min,
        ":max" => $max,
        ":user" => $user
    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__ . "/../partials/flash.php");
