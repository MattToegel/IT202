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
$result = [];
if($tankId > 0){
    $query = "SELECT name, speed, `range`, turnSpeed, fireRate, health, damage FROM tfp_tanks WHERE id = :tid";
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":tid"=> $tankId]);
    
    if($r){
        //fetch
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$result){
            $result = [];
        }
    }
    else{
        flash("Error looking up tank: " . var_export($stmt->errorInfo(), true));
    }
}
?>
<?php if(isset($result) && count($result) > 0):?>
<table style="width:100%">
<thead>
<th>Name</th>
<th>Speed</th>
<th>Range</th>
<th>Turn Speed</th>
<th>Fire Rate</th>
<th>Health</th>
<th>Damage</th>
</thead>
<tbody style="text-align:center">
<tr>
    <?php foreach($result as $key=>$value):?>
        <td><?php echo $value;?></td>
    <?php endforeach;?>
</tr>
</tbody>
</table>
<?php else:?>
<p>Invalid tank selection, or missing query parameter</p>
<?php endif;?>

<?php require(__DIR__ . "/partials/flash.php");?>