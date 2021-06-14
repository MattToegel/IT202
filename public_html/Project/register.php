<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (isset($_POST["submit"])) {
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));
    $confirm = trim(se($_POST, "confirm", null, false));

    $isValid = true;
    if (!isset($email) || !isset($password) || !isset($confirm)) {
        se("Must provide email, password, and confirm password");
        $isValid = false;
    }
    if ($password !== $confirm) {
        se("Passwords don't match");
        $isValid = false;
    }
    if (strlen($password) < 3) {
        se("Password must be 3 or more characters");
        $isValid = false;
    }
    $email = sanitize_email($email);
    if (!is_valid_email($email)) {
        se("Invalid email");
        $isValid = false;
    }
    if ($isValid) {
        //do our registration
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password) VALUES (:email, :password)");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt->execute([":email" => $email, ":password" => $hash]);
        } catch (PDOException $e) {
            $code = se($e->errorInfo, 0, "00000", false);
            if ($code === "23000") {
                se("An account with this email already exists");
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
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;
        if (email) {
            email = email.trim();
        }
        if (password) {
            password = password.trim();
        }
        if (confirm) {
            confirm = confirm.trim();
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