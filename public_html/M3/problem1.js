String.prototype.hashCode = function () {
    var hash = 0, i, chr;
    if (this.length === 0) return hash;
    for (i = 0; i < this.length; i++) {
        chr = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
};
function check() {
    let content = document.body.innerHTML.replace(/\>\s+\</g, '');
    let code = content.hashCode();
    let footer = document.getElementsByTagName("footer")[0];
    console.log(footer);
    footer.innerText = "Verification:" + code;
}