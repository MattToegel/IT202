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
    <ul class="list-group">
         <li class="list-group-item">
            <div class="row fw-bold">
                <div class="col-3">Title</div>
                <div class="col-7">Description</div>
                <div class="col-2">Actions</div>
            </div>
            </li>
        <?php foreach ($results as $res):?>
            <li class="list-group-item">
            <div class="row">
                <div class="col-3"><?php safer_echo($res["title"]);?></div>
                <div class="col-7"><?php safer_echo($res["description"]);?></div>
                <div class="col-2">
                    <?php if(has_role("Admin")):?>
                        <button class="btn btn-secondary">Edit (TBD)</button>
                    <?php endif;?>
                    <a class="btn btn-primary" href="take_survey.php?id=<?php safer_echo($res['id']);?>">Participate</a>
                </div>
            </div>
            </li>
        <?php endforeach;?>
    </ul>
    <?php else:?>
    <p>No results</p>
    <?php endif;?>
</div>
<?php require(__DIR__ . "/partials/flash.php");