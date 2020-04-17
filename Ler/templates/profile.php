<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}

if(isset($_REQUEST['user'])){
    //show provided user
}
else{
    //show logged in user
    $user = Utils::getLoggedInUser(true);
    if($user) {
        //can show data
    }
    if(isset($_POST["updateprofile"])){
        $email = $_POST['email'];
        $username = $_POST['username'];
        $user_id = $user->getID();
        $user_service = $container->getUsers();
        if($user_service->update_profile($user_id, $email, $username)){

            $n = new User($user_id, $username, $email, $user->getRoles());
            Utils::login($n);
            $user = Utils::getLoggedInUser(true);
            //echo "update successful";
            Utils::flash("Update successful");
        }
        else{
            //echo "failed to update";
            Utils::flash("Update failed");
        }
    }
}
?>
<div class="justify-content-center">
    <div class="col">
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" class="form-control" type="text"
                       name="username" value="<?php echo $user->getUsername();?>"/>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" class="form-control"
                       type="email" value="<?php echo $user->getEmail();?>"/>
            </div>
            <input class="btn btn-primary" name="updateprofile" type="submit" value="Save"/>
        </form>
    </div>
</div>