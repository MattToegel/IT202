<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change

$db = getDB();
//save data if we submitted the form
if (isset($_POST["saved"])) {
    //check if our email changed
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["inuse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            echo "Email is already in use";
            //for now we can just stop the rest of the update
            return;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["inUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            echo "Username is already in use";
            //for now we can just stop the rest of the update
            return;
        }
        else {
            $newUsername = $username;
        }
    }
    $stmt = $db->prepare("UPDATE Users set email = :email, username= :username where id = :id");
    $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id()]);
    if ($r) {
        echo "Updated profile";
    }
    else {
        echo "Error updating profile";
    }
    //password is optional, so check if it's even set
    //if so, then check if it's a valid reset request
    if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
        if ($_POST["password"] == $_POST["confirm"]) {
            $password = $_POST["password"];
            $hash = password_hash($password, PASSWORD_BCRYPT);
            //this one we'll do separate
            $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
            $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
            if ($r) {
                echo "Reset password";
            }
            else {
                echo "Error resetting password";
            }
        }
    }
//fetch/select fresh data in case anything changed
    $stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
    $stmt->execute([":id" => get_user_id()]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $email = $result["email"];
        $username = $result["username"];
        //let's update our session too
        $_SESSION["user"]["email"] = $email;
        $_SESSION["user"]["username"] = $username;
    }
}


?>

<form method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" value="<?php echo get_email(); ?>"/>
    <label for="username">Username</label>
    <input type="text" maxlength="60" name="username" value="<?php echo get_username(); ?>"/>
    <!-- DO NOT PRELOAD PASSWORD-->
    <label for="pw">Password</label>
    <input type="password" name="password"/>
    <label for="cpw">Confirm Password</label>
    <input type="password" name="confirm"/>
    <input type="submit" name="saved" value="Save Profile"/>
</form>