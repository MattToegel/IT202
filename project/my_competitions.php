<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();

$stmt = $db->prepare("SELECT c.*, UC.user_id as reg FROM F20_Competitions c LEFT JOIN (SELECT * FROM F20_UserCompetitions where user_id = :id) as UC on c.id = UC.competition_id WHERE c.expires > current_timestamp AND paid_out = 0 AND (UC.user_id = :id OR c.user_id = :id) ORDER BY expires ASC");
$r = $stmt->execute([":id" => get_user_id(),]);
if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem looking up competitions: " . var_export($stmt->errorInfo(), true), "danger");
}
?>
    <div class="container-fluid">
        <h3>Competitions</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <div class="list-group-item font-weight-bold">
                    <div class="row">
                        <div class="col">
                            Name
                        </div>
                        <div class="col">
                            Participants
                        </div>
                        <div class="col">
                            Required Score
                        </div>
                        <div class="col">
                            Reward
                        </div>
                        <div class="col">
                            Expires
                        </div>
                        <div class="col">
                            Actions
                        </div>
                    </div>
                </div>
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">
                                <?php safer_echo($r["name"]); ?>
                                <?php if ($r["user_id"] == get_user_id()): ?>
                                    (Created)
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                <?php safer_echo($r["participants"]); ?>
                            </div>
                            <div class="col">
                                <?php safer_echo($r["min_score"]); ?>
                            </div>
                            <div class="col">
                                <?php safer_echo($r["reward"]); ?>
                                <!--TODO show payout-->
                            </div>
                            <div class="col">
                                <?php safer_echo($r["expires"]); ?>
                            </div>
                            <div class="col">
                                <?php if ($r["reg"] != get_user_id()): ?>
                                    <form method="POST">
                                        <input type="hidden" name="cid" value="<?php safer_echo($r["id"]); ?>"/>
                                        <input type="submit" name="join" class="btn btn-primary"
                                               value="Join (Cost: <?php safer_echo($r["fee"]); ?>)"/>
                                    </form>
                                <?php else: ?>
                                    Already Registered
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No competitions available right now
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require(__DIR__ . "/partials/flash.php");
