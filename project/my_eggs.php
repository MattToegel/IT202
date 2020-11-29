<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

$db = getDB();

$stmt = $db->prepare("SELECT e.*, i.name as inc from F20_Eggs e LEFT JOIN F20_Incubators i on e.id = i.egg_id where e.user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
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
    </div>
<?php require(__DIR__ . "/partials/flash.php");