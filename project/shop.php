<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
    <script>
        function makePurchase() {
            //todo client side balance check
            //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                }
            };
            xhttp.open("POST", "<?php getURL("/api/purchase_egg.php?test=true");?>", true);
            //this is required for post ajax calls to submit it as a form
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //map any key/value data similar to query params
            xhttp.send();

        }
    </script>
    <div class="card">
        <div class="card-title">
            Purchase Random Egg
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-primary btn-lg">Purchase (Price)</button>
        </div>
    </div>
<?php require(__DIR__ . "/partials/flash.php");