<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
$items = [];
$db = getDB();
$query = "SELECT id, name, description, stock, cost, image FROM Items WHERE stock > 0 LIMIT 25";
$stmt = $db->prepare($query);
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $items = $r;
    }
} catch (PDOException $e) {
    //writes to php error log
    //https://www.w3schools.com/php/func_error_log.asp
    error_log("Shop error: " . var_export($e->errorInfo, true));
    flash("Sorry, the shop is unavailable at the moment, please try again later", "warning");
}
?>
<div class="container-fluid">
    <?php $title = "Ye Ol' Shoppe";
    include(__DIR__ . "/../../partials/title.php"); ?>
    <div class="row">
        <?php if ($items && count($items) > 0) : ?>
            <?php foreach ($items as $item) : ?>
                <div class="col pb-5">
                    <div class="card h-100" style="width: 20em">
                        <div class="card-header">
                            Cost: <?php se($item, "cost", 99999); ?>
                        </div>
                        <div class="card-body">
                            <div class="card-title">
                                <?php se($item, "name"); ?>
                            </div>
                            <?php if (!!se($item, "image", false, false) === true) : ?>
                                <div class="text-center">
                                    <img style="max-height: 128px" class="img-fluid" src="<?php se($item, "image"); ?>" />
                                </div>
                            <?php endif; ?>
                            <p class="card-text">
                                <?php se($item, "description"); ?>
                            </p>

                        </div>
                        <button type="button" class="mx-2 mb-1 btn btn-primary" onclick="purchase(event)" id="<?php se($item, "id", -1); ?>">Purchase</button>
                        <div class="card-footer text-muted">
                            Stock: <?php se($item, "stock", 0); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col">Looks like the shop is all sold out, check back later!</div>
        <?php endif; ?>
    </div>
</div>
<script>
    function purchase(e) {
        //my purchase will just support 1 item per click
        //I will not be dealing with bulk quantity
        const product_id = e.target.id;
        //TODO purchase
        if (!!window.jQuery === true) {
            //jQuery version of purchase call
            $.post("api/purchase_item.php", {
                product_id: product_id
            }, res => {
                let data = JSON.parse(res);
                console.log("jQuery response", data);
                if (data.status === 200) {
                    flash("Purchase Successful!", "success");
                    refreshBalance();
                } else {
                    //flash("Error occurred: " + JSON.stringify(data), "danger");
                    flash(data.message, "warning");
                }
            });
        } else {
            //fetch api version of purchase call
            fetch("api/purchase_item.php", {
                method: "POST",
                headers: {
                    "Content-type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: "product_id=" + product_id
            }).then(async res => {
                console.log(res);
                let data = await res.json();
                console.log("fetch response", data);
                if (data.status === 200) {
                    flash("Purchase Successful!", "success");
                    refreshBalance();
                } else {
                    //flash("Error occurred: " + JSON.stringify(data), "danger");
                    flash(data.message, "warning");
                }
            });
        }
        //TODO fetch balance

    }

    function refreshBalance() {
        //jQuery example of ajax
        if (!!window.jQuery === true) {
            $.get("api/get_balance.php", (res) => {
                let data = JSON.parse(res);
                console.log("Fetch balance", data);
                if (data.status === 200) {
                    updateLiveBalance(data.message || 0);
                }
            })
        } else {
            //fetch api version of fetch (if jQuery hasn't been added to the project yet)
            fetch("api/get_balance.php", {
                headers: {
                    //"Content-type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                }
            }).then(async res => {
                console.log(res);
                let json = await res.json();
                console.log(json);
                if (json.status === 200) {
                    updateLiveBalance(data.message || 0);
                }
            });
        }
    }
</script>