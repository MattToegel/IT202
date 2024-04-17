<?php
if (!isset($broker)) {
    error_log("Using Broker partial without data");
    flash("Dev Alert: Broker called without data", "danger");
}
?>
<?php if (isset($broker)) : ?>
    <!-- https://i.kym-cdn.com/entries/icons/original/000/029/959/Screen_Shot_2019-06-05_at_1.26.32_PM.jpg -->
    <div class="card mx-auto" style="width: 18rem;">
        <img src="https://i.kym-cdn.com/entries/icons/original/000/029/959/Screen_Shot_2019-06-05_at_1.26.32_PM.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?php se($broker, "name", "Unknown"); ?> (<?php se($broker, "rarity"); ?>)</h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Life: <?php se($broker, "life", "Unknown"); ?></li>
                    <li class="list-group-item">Power: <?php se($broker, "power", "Unknown"); ?></li>
                    <li class="list-group-item">Defense: <?php se($broker, "defense", "Unknown"); ?></li>
                    <li class="list-group-item">Stonks: <?php se($broker, "stonks", "Unknown"); ?></li>
                </ul>

            </div>

            <?php if (!isset($broker["user_id"]) || $broker["user_id"] === "N/A") : ?>
                <div class="card-body">
                    <a href="<?php echo get_url('api/purchase_broker.php?broker_id=' . $broker["id"]); ?>" class="card-link">Purchase Broker</a>
                </div>
            <?php else : ?>
                <div class="card-body">
                    <div class="bg-warning text-dark text-center">Broker not available</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>