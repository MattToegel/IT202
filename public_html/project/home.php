<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in

    Common::aggregate_stats_and_refresh();
    $transactions = [];
    $result = DBH::get_latest_transactions(Common::get_user_id());
    if(Common::get($result, "status", 400) == 200){
        $transactions = Common::get($result, "data", []);
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div class="container-fluid">
    <h4>Home</h4>
    <p>Welcome, <?php echo Common::get_username();?></p>
    <?php if($last_updated):?>
        <p>Stats Last Updated: <?php echo $last_updated->format('Y-m-d H:i:s');;?></p>
    <?php endif;?>
    <h5>Stats</h5>
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
    <hr/>
    <h5>Latest Activity</h5>
    <table class="table">
        <thread>
            <tr>
                <td>Type</td>
                <td>Amount</td>
                <td>Memo</td>
                <td>Time</td>
            </tr>
        </thread>
        <tbody>
        <?php if(count($transactions) > 0):?>
        <?php foreach($transactions as $t):?>
        <tr>
            <td><?php echo Common::get($t, "type", "?");?></td>
            <td><?php echo Common::get($t, "amount", 0);?></td>
            <td><?php echo Common::get($t, "memo", '');?></td>
            <td><?php echo Common::get($t, "created", '');?></td>
        </tr>
        <?php endforeach;?>
        <?php else:?>
        <tr>
            <td colspan="100%">No activity yet</td>
        </tr>
        <?php endif;?>
        </tbody>
    </table>
</div>
