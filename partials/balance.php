<?php
//requires functions.php to be loaded already
//using $BASE_PATH from functions.php as a check to see if the file was loaded
$balance = 0;
if (isset($BASE_PATH)) {
    $balance = (int)se(get_account_balance(), null, 0, false);
}
?>
<!-- The "component" is the user-balance element.
Chose to make a child element to hold the balance so the rest of the element
can be styled as wished without affecting the liveBalance logic. -->
<div class="user-balance">
    Balance: <span class="balance"><?php echo $balance; ?></span>
</div>
<script>
    //these scripts are to reflect a live balance without refreshing the page
    function registerLiveBalance() {
        let eles = document.getElementsByClassName("user-balance");
        for (ele of eles) {
            //!! courses the value to literally be true or false
            if (!!ele.getAttribute('data-user-balance') === false) {
                console.log("adding listener");
                ele.addEventListener("update-balance", (e) => {
                    console.log("Updated balance", e);
                    e.target.querySelector(".balance").innerText = e.detail.balance || 0; //sets the balance or 0 if not set
                });
                ele.setAttribute('data-user-balance', "true");
            }
        }
    }

    function updateLiveBalance(balance) {
        //https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
        //detail is the payload the event receives. Any custom data defined outside will be dropped/ignored
        const ev = new CustomEvent("update-balance", {
            detail: {
                balance: balance
            }
        });
        let eles = document.getElementsByClassName("user-balance");
        for (ele of eles) {
            ele.dispatchEvent(ev);
        }
    }
    window.onload = registerLiveBalance;
</script>