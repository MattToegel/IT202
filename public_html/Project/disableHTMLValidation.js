/*Open dev tools on the page you want to disable (works for login, register, profile) 
go to the console tab and paste the below code, press enter, it'll disable the html validation until the page reloads.
This is a self executing function so it runs immediately */
//https://gist.github.com/MattToegel/cfc2e1de0023b54a924ab72d74418905
(function disableHTMLValidation () {
    // Get all forms on the page
    const forms = document.forms;

    for (const form of forms) {
        // Get all input elements within each form
        let inputs = form.querySelectorAll("input");

        // Disable validation for each input element
        for (let inp of inputs) {
            // List of attributes to remove
            const attributesToRemove = ["required", "minlength", "maxlength", "min", "max", "step", "pattern"];

            // Iterate over attributes and remove them
            attributesToRemove.forEach(attr => {
                if (inp.hasAttribute(attr)) {
                    inp.removeAttribute(attr);
                    console.log(`Removed ${attr} from element ${inp.name || "[No name]"}`);
                }
            });

            // Change type to text if not already text
            if (!["text", "submit", "reset"].includes(inp.type)) {
                inp.type = "text";
                console.log(`Changed type to text for element ${inp.name || "[No name]"}`);
            }
        }
    }

    alert("HTML Validation has been disabled until page reload");
})();