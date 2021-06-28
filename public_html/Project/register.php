<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (isset($_POST["submit"])) {
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));
    $confirm = trim(se($_POST, "confirm", null, false));
    $username = trim(se($_POST, "username", null, false));

    $isValid = true;
    if (!isset($email) || !isset($password) || !isset($confirm) || !isset($username)) {
        flash("Must provide email, username, password, and confirm password","warning");
        $isValid = false;
    }
    if ($password !== $confirm) {
        flash("Passwords don't match", "warning");
        $isValid = false;
    }
    if (strlen($password) < 3) {
        flash("Password must be 3 or more characters", "warning");
        $isValid = false;
    }
    $email = sanitize_email($email);
    if (!is_valid_email($email)) {
        flash("Invalid email", "warning");
        $isValid = false;
    }
    //TODO add validation for username (length? disallow special chars? etc)
    if ($isValid) {
        //do our registration
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, username, password) VALUES (:email, :username, :password)");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username"=>$username]);
            flash("You've successfully registered, please login");
            die(header("Location: login.php"));
        } catch (PDOException $e) {
            $code = se($e->errorInfo, 0, "00000", false);
            if ($code === "23000") {
                flash("An account with this email already exists", "danger");
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        }
    }
}
?>
<div>
    <h1>Register</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <label for="email">Email: </label>
            <input type="email" id="email" name="email" required />
        </div>
        <div>
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" required />
        </div>
        <div>
            <label for="pw">Password: </label>
            <input type="password" id="pw" name="password" required />
        </div>
        <div>
            <label for="cpw">Confirm Password: </label>
            <input type="password" id="cpw" name="confirm" required />
        </div>
        <div>
            <input type="submit" name="submit" value="Register" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let email = form.email.value;
        let username = form.username.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;
        if (email) {
            email = email.trim();
        }
        if(username)    {
            username = username.trim();
        }
        if (password) {
            password = password.trim();
        }
        if (confirm) {
            confirm = confirm.trim();
        }
        if(!username || username.length === 0)    {
            isValid    =    false;
            alert("Must provide a username");
        }
        if (email.indexOf("@") === -1) {
            isValid = false;
            alert("Invalid email");
        }
        if (password !== confirm) {
            isValid = false;
            alert("Passwords don't match");
        }
        if (password.length < 3) {
            isValid = false;
            alert("Password must be 3 or more characters");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>