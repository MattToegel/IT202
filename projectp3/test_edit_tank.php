<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if(!has_role("Admin")){
    flash("You don't have access to visit this page");
    die(header("Location: login.php"));
}
$tankId = -1;
if(isset($_GET["tankId"])){
    $tankId = $_GET["tankId"];
}
if($tankId <= 0){
    flash("Invalid tank!!!");
}
?>

<?php
if(isset($_POST["submit"]) && $tankId > 0){
    $name = $_POST["name"];
    $speed = $_POST["speed"];
    $range = $_POST["range"];
    $turnSpeed = $_POST["turnSpeed"];
    $fireRate = $_POST["fireRate"];
    $health = $_POST["health"];
    $damage = $_POST["damage"];
    $db = getDB();
    $query = "UPDATE tfp_tanks SET name = :name, speed = :speed, `range`=:range, turnSpeed = :ts, fireRate = :fr, health = :h, damage = :d WHERE id = :tid";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":name"=>$name,
    ":speed"=> $speed,
    ":range" => $range,
    ":ts" => $turnSpeed,
    ":fr"=>$fireRate,
    ":h"=>$health,
    ":d"=>$damage,
     ":tid"=>$tankId]);
    if($r){
        flash("Tank saved successfully");
    }
    else{
        flash("Something went wrong: " . var_export($stmt->errorInfo(), true));
    }
}
?>
<?php
$result = [];
if($tankId > 0){
    $query = "SELECT name, speed, `range`, turnSpeed, fireRate, health, damage FROM tfp_tanks WHERE id = :tid";
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":tid"=> $tankId]);
    if($r){
        //fetch
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    else{
        flash("Error looking up tank: " . var_export($stmt->errorInfo(), true));
    }
}
?>
<?php if(isset($result)):?>
<form method="POST">
    <input name="name" placeholder="Tank Name" value="<?php safer_echo($result["name"]);?>"/>
    <input name="speed" type="number" placeholder="Speed" value="<?php safer_echo($result["speed"]);?>"/>
    <input name="range" type="number" placeholder="Range" value="<?php safer_echo($result["range"]);?>"/>
    <input name="turnSpeed" type="number" placeholder="Turn Speed" value="<?php safer_echo($result["turnSpeed"]);?>"/>
    <input name="fireRate" type="number" placeholder="Fire Rate" value="<?php safer_echo($result["fireRate"]);?>"/>
    <input name="health" type="number" placeholder="Health" value="<?php safer_echo($result["health"]);?>"/>
    <input name="damage" type="number" placeholder="Damage" value="<?php safer_echo($result["damage"]);?>"/>
    <input type="submit" name="submit" value="Edit"/>
</form>
<?php endif;?>
<?php require(__DIR__ . "/partials/flash.php");?>