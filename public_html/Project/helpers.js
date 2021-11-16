function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
    //added to clear out messages after a delay for ajax calls
    //otherwise messages will continue to pile on and block/push content
    setTimeout(() => {
        console.log("removing");
        flash.children[0].remove();
        
    }, 3000);
}
/**
 * Used in AJAX calls to fetch the $_SESSION balance after a server side change
 * to update all the show-balance elements on the page w/o a page refresh
 */
function refreshBalance () {
    fetch("api/get_balance.php", {
        method: "POST",
        headers: {
            "Content-type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest",
        }
    }).then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        let balances = document.getElementsByClassName("show-balance");
        for (let b of balances) {
            b.getElementsByTagName("div")[0].innerText = "Balance: " + (data.balance || 0);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}