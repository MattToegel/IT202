<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
//https://www.digitalocean.com/community/tutorials/how-to-implement-pagination-in-mysql-with-php-on-ubuntu-18-04
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}
$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total = 0;
if($result){
    $total = (int)$result["total"];
}
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;
$stmt = $db->prepare("SELECT e.*, i.name as inc from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id LIMIT :offset, :count");
$stmt->execute([":id"=>get_user_id(), ":offset"=>$offset, ":count"=>$per_page]);
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="container-fluid">
    <h3>My Cart</h3>
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
        <nav aria-label="My Eggs">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page < 1?"disabled":"";?>">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="#"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages?"disabled":"";?>>">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>
<?php require(__DIR__ . "/partials/flash.php");