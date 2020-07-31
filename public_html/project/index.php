<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__."/partials/header.partial.php");
$result = DBH::get_top_10_users_wins();
$lifetime = [];
if(Common::get($result, "status", 400) == 200){
    $lifetime = Common::get($result, "data", []);
}
$now = new DateTime;
$previous_week = new DateTime();
$interval = new DateInterval('P1W');
$previous_week = $previous_week->sub($interval);
$result = DBH::get_top_10_users_wins($previous_week->format("Y-m-d"), $now->format("Y-m-d H:i:s"));
$weekly = [];
if(Common::get($result, "status", 400) == 200){
    $weekly = Common::get($result, "data", []);
}
?>
<div class="container-fluid">
<div class="jumbotron">
    <h1 class="display-4">Welcome to <u>Tanks For Playing!</u></h1>
    <p class="lead">This is a sample project by Matt Toegel for his IT202 Summer class.</p>
    <hr class="my-4">
    <div class="row">
        <div class="col-8">
            <div class="list-group">
                <div class="list-group-item">
                    <p>This is a 'simple' tank game project.</p>
                </div>
                <div class="list-group-item">
                    <p>Each player gets a tank they can level up via the shop.</p>
                </div>
                <div class="list-group-item">
                    <p>Players compete with a dynamic AI tank to progress.</p>
                </div>
                <div class="list-group-item">
                    <p>Various activities award points that can be spent in the shop.</p>
                </div>
                <div class="list-group-item">
                    <p>Register for your account today and get 10 points as a welcome bonus!</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="list-group">
                <div class="list-group-item">
                    <h5>Weekly Scoreboard</h5>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-6">
                                    User
                                </div>
                                <div class="col-6">
                                    Wins
                                </div>
                            </div>
                        </div>
                        <?php foreach($weekly as $row):?>
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-6">
                                        User#<?php echo Common::get($row,"user_id", -1);?>
                                    </div>
                                    <div class="col-6">
                                        <?php echo Common::get($row, "wins", 0);?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                        <?php if(count($weekly) == 0):?>
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        No participants yet
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="list-group-item">
                    <h5>Lifetime Scoreboard</h5>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-6">
                                    User
                                </div>
                                <div class="col-6">
                                    Wins
                                </div>
                            </div>
                        </div>
                        <?php foreach($lifetime as $row):?>
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-6">
                                        User#<?php echo Common::get($row,"user_id", -1);?>
                                    </div>
                                    <div class="col-6">
                                        <?php echo Common::get($row, "wins", 0);?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                        <?php if(count($lifetime) == 0):?>
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        No participants yet
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(!Common::is_logged_in(false)):?>
    <a class="btn btn-primary btn-lg" href="register.php" role="button">Join the Fun!</a>
    <?php endif; ?>
</div>
</div>