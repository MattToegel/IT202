<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<?php
if (isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    try {
        $stmt->execute($params);
    } catch (Exception $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            //TODO come up with a nice error message
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, IFNULL(username, email) as `username` from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            flash("User doesn't exist", "danger");
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again", "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (isset($current_password) && isset($new_password) && isset($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                    }
                }
            } catch (Exception $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            flash("New passwords don't match", "warning");
        }
    }
}
?>

<?php
$email = get_user_email();
$username = get_username();
$id = se($_GET, "id", -1, false);
$isMe = true;
$userData = [];
//Note: I chose to comment out the second condition so I can use the id attribute to present "this" user with a
// "profile view" of their own if the id passed in is the logged in user
//If we just want the logged in user to always see the edit form, uncomment the second condition
if ($id > -1 /*&& $id !== get_user_id()*/) {
    $isMe = false; //we're looking up someone's profile

    $db = getDB();
    $query = "SELECT username, created, last_login from Users where id = :id";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $userData = $r;
            $username = se($userData, "username", "", false);
        }
    } catch (PDOException $e) {
        error_log("Error looking up user $id's profile: " . var_export($e->errorInfo, true));
    }
    $scores = get_latest_scores($id);
}
?>
<div class="container-fluid">

    <?php if ($isMe) : ?>
        <?php $title = "Your Profile";
        include(__DIR__ . "/../../partials/title.php"); ?>
        <a href="?id=<?php se(get_user_id());   ?>" class="btn btn-primary">View Public Profile</a>
        <?php /* Viewing our profile */ ?>
        <form method="POST" onsubmit="return validate(this);">
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" value="<?php se($email); ?>" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" type="text" name="username" id="username" value="<?php se($username); ?>" />
            </div>
            <!-- DO NOT PRELOAD PASSWORD -->
            <div>Password Reset</div>
            <div class="mb-3">
                <label class="form-label" for="cp">Current Password</label>
                <input class="form-control" type="password" name="currentPassword" id="cp" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="np">New Password</label>
                <input class="form-control" type="password" name="newPassword" id="np" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="conp">Confirm Password</label>
                <input class="form-control" type="password" name="confirmPassword" id="conp" />
            </div>
            <input class="btn btn-primary" type="submit" value="Update Profile" name="save" />
        </form>
    <?php else : ?>
        <?php /* Viewing someone elses profile */ ?>
        <?php $title = se($username, null, "", false) . "'s Profile";
        include(__DIR__ . "/../../partials/title.php"); ?>
        <?php if ($userData && count($userData) > 0) : ?>
            <div class="card">
                <div class="card-body">

                    <div class="card-subtitle">
                        <div class="row">
                            <div class="col">
                                Joined: <?php se($userData, "created"); ?>
                            </div>
                            <div class="col">
                                Last Online: <?php se($userData, "last_login"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-text">
                        <div class="row">
                            <div class="col">
                                Best Score: <?php se(get_best_score($id));   ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="fw-bold fs-3">
                                        Last 10 Scores
                                    </div>
                                </div>
                                <div class="card-text">
                                    <table class="table">
                                        <thead>
                                            <th>Score</th>
                                            <th>Achieved</th>
                                        </thead>
                                        <tbody>
                                            <?php if (!$scores || count($scores) == 0) : ?>
                                                <tr>
                                                    <td>No scores available</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($scores as $result) : ?>
                                                    <tr>
                                                        <td><?php se($result, "score"); ?></td>
                                                        <td><?php se($result, "modified"); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <p>There was a problem looking up this user, please try again.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            //find the container
            let flash = document.getElementById("flash");
            //create a div (or whatever wrapper we want)
            let outerDiv = document.createElement("div");
            outerDiv.className = "row justify-content-center";
            let innerDiv = document.createElement("div");

            //apply the CSS (these are bootstrap classes which we'll learn later)
            innerDiv.className = "alert alert-warning";
            //set the content
            innerDiv.innerText = "Password and Confirm password must match";

            outerDiv.appendChild(innerDiv);
            //add the element to the DOM (if we don't it merely exists in memory)
            flash.appendChild(outerDiv);
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>