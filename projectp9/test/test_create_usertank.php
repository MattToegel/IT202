<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if(!has_role("Admin")){
    flash("You don't have access to visit this page");
    die(header("Location: login.php"));
}
?>

<?php
$users = [];
$db = getDB();
$query = "SELECT id, IFNULL(username,email) as `username` from Users LIMIT 100";
$stmt = $db->prepare($query);
$r = $stmt->execute();
if($r){
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
$tanks = [];
$db = getDB();
$query = "SELECT id, name from tfp_tanks LIMIT 100";
$stmt = $db->prepare($query);
$r = $stmt->execute();
if($r){
    $tanks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
if(isset($_POST["userId"]) && isset($_POST["tankId"])){
    $db = getDB();
    $userId = $_POST["userId"];
    $tankId = $_POST["tankId"];
    /*
    INSERT INTO t1 (a,b,c) VALUES (1,2,3)
  ON DUPLICATE KEY UPDATE c=c+1;*/
    $query = "INSERT INTO tfp_usertanks (user_id, tank_id) VALUES (:uid, :tid) ON DUPLICATE KEY UPDATE user_id=:uid, tank_id=:tid";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":uid"=>$userId, ":tid"=>$tankId]);
    if($r){
        flash("WE assigned it");
    }
    else{
        flash("Something bad happened: " . var_export($stmt->errorInfo(), true));
    }
}
?>
<form method="POST">
    <select name="userId">
        <?php foreach($users as $user):?>
            <option value="<?php echo $user['id'];?>"><?php echo $user["username"];?></option>
        <?php endforeach;?>
    </select>
    <select name="tankId">
        <?php foreach($tanks as $tank):?>
            <option value="<?php echo $tank['id'];?>"><?php echo $tank["name"];?></option>
        <?php endforeach;?>
    </select>
    <input type="submit" value="Assign"/>
</form>
<?php require(__DIR__ . "/partials/flash.php");?>