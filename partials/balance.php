<div id="balance-value">
    Balance: <?php echo get_account_balance(); ?>
</div>
<script>
    let bv = document.getElementById("balance-value");
    //I'll make this flexible so I can define various placeholders and copy
    //the value into all of them
    let placeholders = document.getElementsByClassName("show-balance");
    for (let p of placeholders) {
        //https://developer.mozilla.org/en-US/docs/Web/API/Node/cloneNode
        p.innerHTML = bv.outerHTML; //bv.cloneNode(true).outerHTML;
    }
    bv.remove(); //delete the original
    function refresh_balance() {
        postData({}, "/Project/api/get_balance.php").then(data => {
            console.log(data);
            let placeholders = document.getElementsByClassName("show-balance");
            for (let p of placeholders) {
                //https://developer.mozilla.org/en-US/docs/Web/API/Node/cloneNode
                p.innerHTML = `<div>Balance: ${data.balance||0}</div>`;
            }
        })

    }
</script>