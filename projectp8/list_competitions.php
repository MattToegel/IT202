<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to waste...spend your points");
    die(header("Location: login.php"));
}
?>
<?php
$query = "SELECT c.*, (select count(1) from tfp_usercompetitions uc where uc.competition_id = c.id and uc.user_id = :uid) as `registered` FROM tfp_competitions c WHERE expires > CURDATE() AND calced_winner != 1 ORDER BY expires asc LIMIT 50";
$db = getDB();
$stmt = $db->prepare($query);
$r = $stmt->execute([":uid" => get_user_id()]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//echo var_export($results, true);

?>
<div class="container-fluid">
    <div class="h3">Active Competitions</div>
    <?php if (count($results) > 0) : ?>
        <ul class="list-group">
            <?php foreach ($results as $c) : ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col"><?php safer_echo(safe_get($c, "title", "N/A")); ?></div>
                        <div class="col">Participants: <?php safer_echo(safe_get($c, "participants", 0)); ?> / <?php safer_echo(safe_get($c, "min_participants", 0)); ?></div>
                        <div class="col">Ends: <?php safer_echo(safe_get($c, "expires", "N/A")); ?></div>
                        <div class="col">Reward: <?php safer_echo(safe_get($c, "points", 0)); ?></div>
                        <div class="col">
                            <?php if (safe_get($c, "registered", 0) == 0) : ?>
                                <button id="<?php safer_echo(safe_get($c, 'id', -1)); ?>" class="btn btn-primary" onclick="join(<?php safer_echo(safe_get($c, 'id', -1)); ?>)">Join (<?php $cost = (int)safe_get($c, "entry_fee", 0);
                                                                                                                                                                                    safer_echo($cost ? "Cost: $cost" : "Cost: Free"); ?>)
                                </button>
                            <?php else : ?>
                                <button class="btn btn-secondary" disabled="disabled">Already registered
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No competitions available yet, please check back later</p>
    <?php endif; ?>
</div>
<script>
    function join(compId) {
        $.post("api/join_competition.php", {
            compId: compId
        }, (data, status) => {
            console.log(data, status);
            let resp = JSON.parse(data);
            if (resp.status === 200) {
                let button = document.getElementById(compId);
                button.disabled = true;
                button.innerText = "Already Registered";
            }
            alert(resp.message);
        });
    }
</script>
<?php require(__DIR__ . "/partials/flash.php");
