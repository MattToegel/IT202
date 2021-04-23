<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if(!has_role("Admin")){
    flash("You don't have access to visit this page");
    die(header("Location: " . getURL("login.php")));
}
?>

<?php
$results = [];
if(isset($_POST["tankName"])){
    $tankName = $_POST["tankName"];
    $isValid = true;
    if(empty(trim($tankName))){
        flash("You must provide a search criteria");
        $isValid = false;
    }
    if($isValid){
        $db = getDB();
        $query = "SELECT id, name from tfp_tanks WHERE name LIKE :name";
        $stmt = $db->prepare($query);
        $r = $stmt->execute([":name"=>"%$tankName%"]);
        
        if($r){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
            flash("There was a problem fetching the results" . var_export($stmt->errorInfo(), true));
        }
    }
}
?>
<form method="POST">
<label for="tn">Tank Name</label>
<input type="text" name="tankName" id="tn"/>
<input type="submit" value="Search"/>
</form>

<table>
<thead>
<th>Tank ID</th>
<th>Tank Name</th>
<th>Actions</th>
</thead>
<tbody>
    <?php if(isset($results) && count($results) > 0):?>
        <?php foreach($results as $r):?>
            <tr>
                <td><?php echo $r["id"];?></td>
                <td><?php echo $r["name"];?></td>
                <td>
                    <a type="button" href="<?php echo getURL("test/test_edit_tank.php");?>?tankId=<?php echo $r['id'];?>">Edit</a>
                    <a type="button" href="<?php echo getURL("test/test_view_tank.php");?>?tankId=<?php echo $r['id'];?>">View</a>
                </td>
            </tr>
        <?php endforeach;?>
    <?php else:?>
        <tr>
        <td colspan="100%">
            No Results
        </td>
        </td>
    <?php endif;?>
</tbody>
</table>
<?php require(__DIR__ . "/../partials/flash.php");?>