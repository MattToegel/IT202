<?php
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
session_set_cookie_params([
    "lifetime" => 60 * 60,
    "path" => "/Project",
    //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
    "domain" => $domain,
    "secure" => true,
    "httponly" => true,
    "samesite" => "lax"
]);
session_start();
require_once(__DIR__ . "/../lib/functions.php");

?>





<head>
    <title></title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- jQuery 3.6.0 min-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <style>
        label,
        th {
            text-transform: capitalize;
        }
    </style>
</head>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php if (is_logged_in()) : ?>
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="quarry.php">Quarry</a></li>
                <li class="nav-item"><a class="nav-link" href="account_history.php">Account History</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Competitions
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="create_competition.php">Create</a></li>
                        <li><a class="dropdown-item" href="competitions.php">Active</a></li>
                        <li><a class="dropdown-item" href="competitions.php?filter=joined">Joined History</a></li>
                        <li><a class="dropdown-item" href="competitions.php?filter=expired">Expired</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (!is_logged_in()) : ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <?php endif; ?>
            <?php if (has_role("Admin")) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="add_item.php">Add Item</a></li>
                        <li><a class="dropdown-item" href="add_score.php">Add Score</a></li>
                        <li><a class="dropdown-item" href="view_user_accounts.php">View Accounts</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (is_logged_in()) : ?>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php endif; ?>
        </ul>
        <?php if (is_logged_in()) :    ?>
            <?php include(__DIR__ . "/balance.php"); ?>
        <?php endif;    ?>
    </div>
</nav>
<script>
    //js based flash (requires the flash system/components I provided);
    function flash(message, color = "info") {
        //debugger; //<-- this is used to trigger the debugger in dev tools console if this line executes
        //uncomment it to see
        /*refer to flash.php for template to follow*/
        if (!!window.jQuery === true) {
            //jQuery implementation
            let wrapper = $("<div></div>"); //create new element
            wrapper.addClass("row justify-content-center");
            let msg = $("<div></div>");
            msg.addClass("alert alert-" + color);
            msg.role = "alert";
            msg.text(message);
            wrapper.append(msg);
            if ($("#flash").length === 0) {
                let container = $("<div></div>");
                container.attr("id", "flash");
                container.addClass("container");
                container.insertAfter("nav");
            }
            $("#flash").append(wrapper);
        } else {
            //vanilla js implementation
            let wrapper = document.createElement("div");
            wrapper.className = "row justify-content-center";
            let msg = document.createElement("div");
            msg.className = "alert alert-" + color;
            msg.innerText = message;
            wrapper.appendChild(msg);
            if (!document.getElementById("flash")) {
                let container = document.createElement("div");
                container.className = "container";
                container.id = "flash";
                document.getElementsByTagName("nav")[0].after(container);
            }
            document.getElementById("flash").appendChild(wrapper);
        }
    }
</script>