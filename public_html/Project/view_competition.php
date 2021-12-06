<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$db = getDB();
//handle join
if (isset($_POST["join"])) {
    $user_id = get_user_id();
    $comp_id = se($_POST, "comp_id", 0, false);
    $cost = se($_POST, "join_cost", 0, false);
    $balance = get_account_balance();
    if ($comp_id > 0) {
        if ($balance >= $cost) {
            join_competition($comp_id, $user_id);
        } else {
            flash("You can't afford to join this competition", "warning");
        }
    } else {
        flash("Invalid competition, please try again", "danger");
    }
}
$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid competition", "danger");
    redirect("list_competitions.php");
}
//TODO fetch 1
//TODO show comp title on page
//TODO show current scoreboard (top 10)
$per_page = 5;
paginate("SELECT count(1) as total FROM BGD_Competitions WHERE xpires > current_timestamp() AND did_payout < 1 AND did_calc < 1");
//handle page load
$stmt = $db->prepare("SELECT BGD_Competitions.id, title, min_participants, current_participants, current_reward, expires, creator_id, min_score, join_cost, IF(competition_id is null, 0, 1) as joined,  CONCAT(first_place,'% - ', second_place, '% - ', third_place, '%') as place FROM BGD_Competitions
JOIN BGD_Payout_Options on BGD_Payout_Options.id = BGD_Competitions.payout_option
LEFT JOIN BGD_UserComps on BGD_UserComps.competition_id = BGD_Competitions.id WHERE user_id = :uid AND BGD_Competitions.id = :cid");
$results = [];
try {
    $stmt->execute([":uid" => get_user_id(), ":cid" => $id]);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("There was a problem fetching competitions, please try again later", "danger");
    error_log("List competitions error: " . var_export($e, true));
}
?>
<div class="container-fluid">
    <h1>View Competition</h1>
    <table class="table text-light">
        <thead>
            <th>Title</th>
            <th>Participants</th>
            <th>Reward</th>
            <th>Min Score</th>
            <th>Expires</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php if (count($results) > 0) : ?>
                <?php foreach ($results as $row) : ?>
                    <td><?php se($row, "title"); ?></td>
                    <td><?php se($row, "current_participants"); ?>/<?php se($row, "min_participants"); ?></td>
                    <td><?php se($row, "current_reward"); ?><br>Payout: <?php se($row, "place", "-"); ?></td>
                    <td><?php se($row, "min_score"); ?></td>
                    <td><?php se($row, "expires", "-"); ?></td>
                    <td>
                        <?php if (se($row, "joined", 0, false)) : ?>
                            <button class="btn btn-primary disabled" onclick="event.preventDefault()" disabled>Already Joined</button>
                        <?php else : ?>
                            <form method="POST">
                                <input type="hidden" name="comp_id" value="<?php se($row, 'id'); ?>" />
                                <input type="hidden" name="cost" value="<?php se($row, 'join_cost', 0); ?>" />
                                <input type="submit" name="join" class="btn btn-primary" value="Join (Cost: <?php se($row, "join_cost", 0) ?>)" />
                            </form>
                        <?php endif; ?>


                    </td>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="100%">No active competitins</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
</div>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>