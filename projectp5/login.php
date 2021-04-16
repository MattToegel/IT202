<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="container-fluid">
    <form method="POST">
        <label class="form-label" for="email">Email:</label>
        <input class="form-control" type="email" id="email" name="email" required/>
        <label class="form-label" for="p1">Password:</label>
        <input class="form-control" type="password" id="p1" name="password" required/>
        <input class="btn btn-primary mt-2" type="submit" name="login" value="Login"/>
    </form>
</div>
<?php
if (isset($_POST["login"])) {
    $email = null;
    $password = null;
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    $isValid = true;
    if (!isset($email) || !isset($password)) {
        $isValid = false;
         flash("Email or password missing");
    }
    if (!strpos($email, "@")) {
        $isValid = false;
        flash("Invalid email");
    }
    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
            $stmt = $db->prepare("SELECT id, email, username, password from Users WHERE email = :email LIMIT 1");

            $params = array(":email" => $email);
            $r = $stmt->execute($params);
            //echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong, please try again");
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];
                if (password_verify($password, $password_hash_from_db)) {
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password so we don't leak it beyond this page
                    //let's create a session for our user based on the other data we pulled from the table
                    $_SESSION["user"] = $result;//we can save the entire result array since we removed password
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles;
                    }
                    else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //TODO move to function so it can be reused
                    $query = "SELECT t.id, t.name, t.speed, t.range, t.turnSpeed, t.fireRate, t.health, t.tankColor, t.barrelColor, t.barrelTipColor, t.treadColor, t.hitColor, t.gunType, t.damage from tfp_tanks t JOIN tfp_usertanks ut ON t.id = ut.tank_id where ut.user_id = :uid";
                    $stmt = $db->prepare($query);
                    $stmt->execute([":uid"=>$result["id"]]);
                    $tanks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    flash(var_export($tanks, true));
                    $_SESSION["user"]["tanks"] = $tanks;
                    //end TODO
                    //on successful login let's serve-side redirect the user to the home page.

                    $query = "SELECT DATEDIFF(CURRENT_TIMESTAMP, modified) as d FROM tfp_userstats WHERE user_id = :uid";
                    $stmt = $db->prepare($query);
                    $r = $stmt->execute([":uid"=>$result["id"]]);
                    if($r){
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($result && safe_get($result, "d", 0) > 0){
                            if(changePoints(get_user_id(), 1, "Login bonus")){
                                flash("You get a login bonus!");
                            }
                            
                        }
                    }

                    flash("Log in successful");
                    die(header("Location: home.php"));
                }
                else {
                    flash("Invalid password");
                }
            }
            else {
                flash("Invalid user");
            }
        }
    }
    else {
         flash("There was a validation issue");
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");