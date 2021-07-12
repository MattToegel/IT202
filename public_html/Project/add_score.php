<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
?>
<div>
    <button onclick="save_score_xmlhttprequest()">Give yourself a cookie (xmlhttprequest)</button>
    <button onclick="save_score_fetch()">Give yourself a cookie (fetch api)</button>
    <button onclick="save_score_jquery()">Give yourself a cookie (jquery)</button>
</div>
<script>
    function save_score_xmlhttprequest() {
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                alert(this.responseText);
            }
        };
        xhttp.open("POST", "api/save_score.php", true);
        //if we send data we need to set this header to have it process the data properly
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //if testing on a different domain or local you'll have to set this header
        //refer here: https://stackoverflow.com/a/13005183
        xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhttp.send("score=1");
    }

    function save_score_fetch() {
        //https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API
        //https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
        fetch("api/save_score.php", {
            method: "POST",
            //if testing on a different domain or local you'll have to set this header
            //refer here: https://stackoverflow.com/a/13005183
            headers: {
                "Content-type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: "score=1"
        }).then(async resp => {
            //uses a promise so we need to await it: https://www.w3schools.com/js/js_promise.asp
            let json = await resp.text(); //can only call this once
            console.log(resp, json);
            alert(json);
        }).catch(err => {
            alert("Error: " + err);
        })
    }

    function save_score_jquery() {
        //https://www.w3schools.com/jquery/jquery_ajax_get_post.asp
        //TBD
        $.post("api/save_score.php", {
            score: 1
        }, (res) => {
            console.log("resp", res);
            alert(res);
        });
    }
</script>