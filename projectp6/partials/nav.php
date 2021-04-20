<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
<!--<link rel="stylesheet" href="static/css/styles.css">-->
<script
  src="https://code.jquery.com/jquery-3.6.0.js"
  integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
  crossorigin="anonymous"></script>
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav class="navbar navbar-expand-lg navbar-info bg-info">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="<?php echo getURL("home.php");?>">Home</a></li>
            <?php if (!is_logged_in(false)) : ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURL("login.php");?>">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURL("register.php");?>">Register</a></li>
            <?php endif; ?>
            <?php if (has_role("Admin")) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo getURL("test/test_create_tank.php");?>">Create Tank</a></li>
                        <li><a class="dropdown-item" href="<?php echo getURL("test/test_list_tanks.php");?>">List Tanks</a></li>
                        <li><a class="dropdown-item" href="<?php echo getURL("create_survey.php");?>">Create Survey</a></li>
                        <li><a class="dropdown-item" href="<?php echo getURL("search_surveys.php");?>">List Surveys</a></li>
                    </ul>
                </li><?php endif; ?>
            <?php if (is_logged_in(false)) : ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURL("profile.php");?>">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURL("logout.php");?>">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>