<?php
$db = getDB();
$results = [];
if (!isset($user_id)) {
    $user_id = get_user_id();
}
error_log("inventory");
$stmt = $db->prepare("SELECT i.id, name, image, quantity FROM BGD_Inventory inv JOIN BGD_Items i on inv.item_id = i.id WHERE inv.user_id = :uid and quantity > 0");
try {
    $stmt->execute([":uid" => $user_id]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}
//TODO
//display inventory output
//allow triggering effects for next game session
//store triggered items in a new table (so it persists between page loads and logouts)
?>
<h5>Inventory</h5>
<div class="row row-cols-3 g-4">
    <?php foreach ($results as $r) : ?>
        <div class="col ">
            <div class="card bg-dark ">
                <div class="card-body">
                    <div class="card-text"><?php se($r, "name"); ?></div>
                </div>
                <div class="card-footer">
                    <div class="quantity">
                        <?php se($r, "quantity", 0); ?>
                    </div>
                    <button onclick="activate_item(<?php se($r, 'id'); ?>, this)">Use</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>