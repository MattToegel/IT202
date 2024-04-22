<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php
$id = se($_GET, "id", -1, false);


$broker = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT name, rarity, life, power, defense, stonks, created, modified FROM `IT202-S24-Brokers` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $broker = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    redirect("admin/list_brokers.php");
}
foreach ($broker as $key => $value) {
    if (is_null($value)) {
        $broker[$key] = "N/A";
    }
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Broker: <?php se($broker, "name", "Unknown"); ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_brokers.php"); ?>" class="btn btn-secondary">Back</a>
    </div>

    <?php render_broker_card($broker); ?>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>