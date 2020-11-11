<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
$balance = getBalance();
$cost = calcNextEggCost();
?>
    <script>
        //php will exec first so just the value will be visible on js side
        let balance = <?php echo $balance;?>;
        let cost = <?php echo $cost;?>;

        function makePurchase() {
            //todo client side balance check
            if (cost > balance) {
                alert("You can't afford this right now");
                return;
            }
            //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    let json = JSON.parse(this.responseText);
                    if (json) {
                        if (json.status == 200) {
                            alert("Congrats you received 1 " + json.egg.name);
                            location.reload();
                        } else {
                            alert(json.error);
                        }
                    }
                }
            };
            xhttp.open("POST", "<?php echo getURL("api/purchase_egg.php");?>", true);
            //this is required for post ajax calls to submit it as a form
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //map any key/value data similar to query params
            xhttp.send();

        }
    </script>
    <div class="container-fluid">
        <div class="col-2">
            <div class="card">
                <div class="card-title">
                    Purchase Random Egg
                </div>
                <div class="card-body">
                    <button type="button" onclick="makePurchase();" class="btn btn-primary btn-lg">Purchase
                        (Cost: <?php echo $cost; ?>)
                    </button>
                </div>
            </div>
        </div>
        <div class="col-2">
            <div class="card">
                <div class="card-title">
                    Purchase Random Incubator
                </div>
                <div class="card-body">
                    <button type="button" onclick="alert('Coming soon');" class="btn btn-primary btn-lg">Purchase
                        (Cost: <?php echo $cost; ?>)
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php require(__DIR__ . "/partials/flash.php");
