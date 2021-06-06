<body onload="getValidBdayRange()">
    <script>
        function getValidBdayRange() {
            //using this for two purposes, it's a bit sloppy in this case, but it's ok for the example
            let ele = document.forms[0].bday;
            let today = new Date();
            let minAge = 13;
            today.setFullYear(today.getFullYear() - minAge);
            //https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toISOString
            today = today.toISOString().split("T")[0];
            ele.max = today;
            return today;
        }

        function isFormValid(form) {
            let isValid = true;
            //validate first name
            let fn = form.fn.value;
            let fnv = document.getElementById("fnv");
            fnv.innerText = "";
            if (!fn) { //it's a falsey value (empty, null, undefined)
                fnv.innerText = "You must provide your first name";
                isValid = false;
            }
            let namePattern = /[A-Z][a-z]+/;
            let regex = new RegExp(namePattern);
            if (!fnv.innerText && !regex.test(fn)) {
                fnv.innerText = "Please enter only alphabetical characters with the first character capitalized";
                isValid = false;
            }
            //validate last name
            let ln = form.ln.value;
            let lnv = document.getElementById("lnv");
            lnv.innerText = "";
            if (!ln) {
                lnv.innerText = "You must provide your last name";
                isValid = false;
            }
            if (!lnv.innerText && !regex.test(ln)) {
                lnv.innerText = "Please enter only alphabetical characters with the first character capitalized";
                isValid = false;
            }
            //age validation
            //age between 13-120
            let age = form.age.value || 0; // if no value is set it'll default to 0
            let agev = document.getElementById("agev");
            agev.innerText = "";
            //this is to catch non-numeric values, could also use isNaN(), update try/catch doesn't work here either
            try {
                age = parseInt(age);    //returns NaN or the number
                if (isNaN(age)) {
                    throw new Exception("Not a number");
                }
            } catch (e) {
                agev.innerText = "Please enter a valid age, it must be a whole number";
                isValid = false;
            }
            if (!agev.innerText && (age < 13 || age > 120)) {
                agev.innerText = "Sorry, you must be 13 years or older to register for this site";
                //we'll ignore the 120 part for a validation message
                isValid = false;
            }
            //birthday validation
            let bday = form.bday.value;
            let bdayv = document.getElementById("bdayv");
            bdayv.innerText = "";
            console.log("bday", bday);
            if (!bday) {
                bdayv.innerText = "Please enter your birthday";
                isValid = false;
            }
            let birthdayDate;
            //NOTE: here the try/catch block doesn't work
            try {
                console.log("Received date", bday);
                birthdayDate = new Date(bday);    //returns the date or "Invalid Date"
                console.log("birthday before", birthdayDate);
                //We need to add 1 to the day https://stackoverflow.com/a/1507625   :)
                birthdayDate.setDate(birthdayDate.getDate() + 1);
                console.log("Note the value and type", birthdayDate, typeof birthdayDate);
                //using == here since the type is an object, could also have done birthdayDate.toString() === "Invalid Date"
                if (!bdayv.innerText && birthdayDate == "Invalid Date") {
                    bdayv.innerText = "Please enter a valid date";
                    isValid = false;
                }
            } catch (e) {
                //note this doesn't "catch"
                bdayv.innerText = "Please enter a valid date";
                isValid = false;
            }
            let minDateString = getValidBdayRange();
            let minDate = new Date(minDateString);
            console.log("min date", minDate);
            if (!bdayv.innerText && birthdayDate > minDate) {
                //ignoring the 120 limit
                bdayv.innerText = "Your birthday must be on this date or earlier: " + minDateString;
                isValid = false;
            }
            return isValid;
        }
    </script>
    <h1>Form and JS Validation Practice</h1>
    <h3><a href="index.php">Back</a></h3>
    <form method="post" onsubmit="return isFormValid(this);">
        <div>
            <label for="fn">First Name:</label>
            <input name="fn" id="fn" pattern="[A-Z][a-z]+" title="Please enter only alphabetical characters with the first character capitalized" />
            <span id="fnv"></span>
        </div>
        <div>
            <label for="ln">Last Name:</label>
            <input name="ln" id="ln" pattern="[A-Z][a-z]+" title="Please enter only alphabetical characters with the first character capitalized" />
            <span id="lnv"></span>
        </div>
        <div>
            <label for="age">Age:</label>
            <input type="number" name="age" min="13" max="120" />
            <span id="agev"></span>
        </div>
        <div>
            <label for="bday">Birthday:</label>
            <input type="date" name="bday" />
            <span id="bdayv"></span>
        </div>
        <input type="submit" name="submit" />
    </form>
</body>
<?php
echo '$_GET<br>';
if (isset($_GET["submit"])) {
    echo "<pre>" . var_export($_GET, true) . "</pre>";
}
echo '$_POST<br>';
if (isset($_POST["submit"])) {
    echo "<pre>" . var_export($_POST, true) . "</pre>";
}
echo '$_REQUEST<br>';
if (isset($_REQUEST["submit"])) {
    echo "<pre>" . var_export($_REQUEST, true) . "</pre>";
}

?>