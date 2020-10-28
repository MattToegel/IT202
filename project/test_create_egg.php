<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<form method="POST">
	<label>Name</labe>
	<input name="name" placeholder="Name"/>
	<label>State</label>
	<select name="state">
		<option value="0">Incubating</option>
		<option value="1">Hatching</option>
		<option value="2">Hatched</option>
		<option value="3">Expired</option>
	</select>
	<label>Base Rate</label>
	<input type="number" min="1" name="base_rate"/>
	<label>Mod Min</label>
	<input type="number" min="1" name="mod_min"/>
	<label>Mod Max</label>
	<input type="number" min="1" name="mod_max"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$state = $_POST["state"];
	$br = $_POST["base_rate"];
	$min = $_POST["mod_min"];
	$max = $_POST["mod_max"];
	$nst = date('Y-m-d H:i:s');//calc
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO F20_Eggs (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
	$r = $stmt->execute([
		":name"=>$name,
		":state"=>$state,
		":br"=>$br,
		":min"=>$min,
		":max"=>$max,
		":nst"=>$nst,
		":user"=>$user
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php");
