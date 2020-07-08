<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in

    Common::aggregate_stats_and_refresh();
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
