<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in

    //poor man's cronjob - run commands no sooner than specified delay
    $first = false;
    if(isset($_SESSION["last_sync"])){
        $last_sync = $_SESSION["last_sync"];
    }
    else{
        $last_sync = new DateTime();
        $_SESSION["last_sync"] = $last_sync;
        $first = true;
    }
    $seconds = Common::get_seconds_since_dates($last_sync);
    if($first || $seconds >= 120){//no sooner than every 2 mins
        //TODO aggregate user XP, Points, and Level
        $user_id = Common::get_user_id();
        $result = DBH::get_aggregated_stats($user_id);
        $result = Common::get($result, "data", false);
        if($result){
            error_log(var_export($result, true));
            $xp = Common::get($result, "XP", 0);
            $points = Common::get($result, "Points", 0);
            $wins = Common::get($result, "Wins", 0);
            $losses = Common::get($result, "Losses", 0);
            $level = (int)($xp/100)+1;//TODO implement different leveling system
            $result = DBH::update_user_stats($user_id, $level, $xp, $points, $wins, $losses);
            error_log(var_export($result, true));
            $_SESSION["user"]["experience"] = $xp;
            $_SESSION["user"]["level"] = $level;
            $_SESSION["user"]["points"] = $points;
            $_SESSION["user"]["wins"] = $wins;
            $_SESSION["user"]["losses"] = $losses;
            if(Common::get($result, "status", 400) == 200){
                $_SESSION["last_sync"] = new DateTime();
            }

        }
        //update time
        $last_sync = new DateTime();
        $_SESSION["last_sync"] = $last_sync;
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div>
    <p>Welcome, <?php echo Common::get_username();?></p>
    <?php if($last_updated):?>
        <p>Last Updated: <?php echo $last_updated->format('Y-m-d H:i:s');;?></p>
    <?php endif;?>
    <table class="table">
        <thread>
            <tr>
                <td>Level</td>
                <td>Experience</td>
                <td>Points</td>
                <td>Wins</td>
                <td>Losses</td>
            </tr>
        </thread>
        <tbody>
            <tr>
                <td><?php echo Common::get($_SESSION["user"], "level", 0);?></td>
                <td><?php echo Common::get($_SESSION["user"], "experience", 0);?></td>
                <td><?php echo Common::get($_SESSION["user"], "points", 0);?></td>
                <td><?php echo Common::get($_SESSION["user"], "wins", 0);?></td>
                <td><?php echo Common::get($_SESSION["user"], "losses", 0);?></td>
            </tr>
        </tbody>
    </table>
</div>
