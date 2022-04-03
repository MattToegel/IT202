function flash (message = "", color = "info") {
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
}
function isValidUsername (username) {
    const pattern = /^[a-z0-9_-]{3,16}$/;
    return pattern.test(username);
}
function isValidEmail (email) {

}
function isValidPassword (password) {
    if (!password) {
        return false;
    }
    return password.length >= 8;
}
function isEqual (a, b) {
    return a === b;
}