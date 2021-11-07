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
        p.innerHTML = bv.cloneNode(true).outerHTML;
    }
    bv.remove(); //delete the original
</script>