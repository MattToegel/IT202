<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if(!has_role("Admin")){
    flash("You don't have access to visit this page");
    die(header("Location: " . getURL("login.php")));
}
?>

<?php
if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $db = getDB();
    $query = "INSERT INTO tfp_tanks (name, user_id) VALUES (:name, :uid)";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":name"=>$name, ":uid"=>get_user_id()]);
    if($r){
        flash("Tank created successfully");
    }
    else{
        flash("Something went wrong: " . var_export($stmt->errorInfo(), true));
    }
}
?>
<form method="POST">
    <input name="name" placeholder="Tank Name"/>
    <input type="submit" name="submit" value="Create"/>
</form>
<?php require(__DIR__ . "/../partials/flash.php");?>