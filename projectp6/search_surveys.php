<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have access to visit this page");
    die(header("Location: login.php"));
}
?>
<?php 
$search = "";
if(isset($_GET["query"])){
    $search = $_GET["query"];
}
$results = [];
if(isset($search) && !empty($search)){
    $db = getDB();
    $query = "SELECT * from tfp_surveys WHERE title like :t LIMIT 50";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":t"=>"%$search%"]);
    if($r){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="container-fluid">
<div class="h3">Search Surveys</div>
<form>
    <input type="text" name="query" value="<?php safer_echo($search);?>"/>
    <input type="submit" value="Search"/>
</form>
    <?php if(count($results)>0):?>
    <ul>
        <?php foreach ($results as $res):?>
            <li><?php safer_echo($res["title"]) . " (" . safer_echo($res["visibility"]) . ")"?></li>
        <?php endforeach;?>
    </ul>
    <?php else:?>
    <p>No results</p>
    <?php endif;?>
</div>