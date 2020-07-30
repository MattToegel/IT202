<?php
include_once(__DIR__."/partials/header.partial.php");
?>
    <div class="container-fluid">
        <h4>Register</h4>
        <form method="POST">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required/>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required min="3"/>
            </div>
            <div>
                <label for="cpassword">Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" required min="3"/>
            </div>
            <input type="submit" name="submit" value="Register"/>
        </form>
    </div>
<?php
if (Common::get($_POST, "submit", false)){
    $email = Common::get($_POST, "email", false);
    $password = Common::get($_POST, "password", false);
    $confirm_password = Common::get($_POST, "cpassword", false);
    if($password != $confirm_password){
        Common::flash("Passwords must match", "warning");
        die(header("Location: register.php"));
    }
    if(!empty($email) && !empty($password)){
        $result = DBH::register($email, $password);
        echo var_export($result, true);
        if(Common::get($result, "status", 400) == 200){
            //Note to self: Intentionally didn't add tank creation here
            //keeping it in login where it is (creates a new tank only if user has no tanks)
            //it fulfills the purpose there
            Common::flash("Successfully registered, please login", "success");
            $data = Common::get($result, "data", []);
            $id = Common::get($data,"user_id", -1);
            if($id > -1) {
                $result = DBH::changePoints($id, 10, -1, "earned", "Welcome bonus");
                if(Common::get($result, "status", 400) == 200){
                    Common::flash("Here's 10 free points for the shop to start you off!", "success");
                }
            }
            die(header("Location: " . Common::url_for("login")));
        }
    }
    else{
        Common::flash("Email and password must not be empty", "warning");
        die(header("Location: register.php"));
    }
}