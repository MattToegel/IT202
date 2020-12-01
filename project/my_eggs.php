<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
//https://www.digitalocean.com/community/tutorials/how-to-implement-pagination-in-mysql-with-php-on-ubuntu-18-04

$per_page = 10;

$db = getDB();
$query = "SELECT count(*) as total from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id";
$params = [":id"=>get_user_id()];
paginate($query, $params, $per_page);
/*$stmt = $db->prepare("SELECT count(*) as total from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total = 0;
if($result){
    $total = (int)$result["total"];
}
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;*/
$stmt = $db->prepare("SELECT e.*, i.name as inc from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id LIMIT :offset, :count");
//need to use bindValue to tell PDO to create these as ints
//otherwise it fails when being converted to strings (the default behavior)
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id());
$stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="container-fluid">
    <h3>My Eggs</h3>
    <div class="row">
    <div class="card-group">
<?php if($results && count($results) > 0):?>
    <?php foreach($results as $r):?>
        <div class="col-auto mb-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <div class="card-title">
                        <?php safer_echo($r["name"]);?>
                    </div>
                    <div class="card-text">
                        <div>Current State: <?php getState($r["state"]); ?></div>
                        <?php if(isset($r["inc"])):?>
                            Incubating in <?php safer_echo($r["inc"]);?>
                        <?php else:?>
                            Not incubating
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div>Next Stage: <?php safer_echo($r["next_stage_time"]); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php else:?>
<div class="col-auto">
    <div class="card">
       You don't have any eggs.
    </div>
</div>
<?php endif;?>
    </div>
    </div>
        <?php include(__DIR__."/partials/pagination.php");?>
    </div>
<?php require(__DIR__ . "/partials/flash.php");
