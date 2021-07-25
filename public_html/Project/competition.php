<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
$comp_id = (int)se($_GET, "id", -1, false);
if ($comp_id < 1) {
    flash("Invalid competition", "danger");
    die(header("Location: competitions.php"));
}

$result = [];
$db = getDB();
$query = "SELECT name, creator, username, current_reward, min_participants, current_participants, payouts, c.created, expires
FROM Competitions c JOIN Users u on u.id = c.creator WHERE c.id = :cid";
$stmt = $db->prepare($query);
try {
    $stmt->execute([":cid" => $comp_id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $result = $r;
    }
} catch (PDOException $e) {
    error_log("Error looking up competition details: " . var_export($e->errorInfo, true));
}
$top = [];
if (!!$result === true) {
    $top = get_competition_top($comp_id);
}
?>
<div class="container-fluid">
    <?php $title = "Competition " . se($result, "name", "", false);
    include(__DIR__ . "/../../partials/title.php"); ?>
    <div class="card">
        <div class="card-body">
            <div class="card-subtitle">
                <div class="row">
                    <div class="col">
                        Created By: <a href="profile.php?id=<?php se($result, "creator"); ?>"><?php se($result, "username"); ?></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        Created: <?php se($result, "created"); ?>
                    </div>
                    <div class="col">
                        Ends: <?php se($result, "expires"); ?>
                    </div>
                </div>
            </div>
            <div class="card-text">
                <table class="table">
                    <thead>
                        <th>Place</th>
                        <th>User</th>
                        <th>Score</th>
                    </thead>
                    <tbody>
                        <?php if ($top && count($top) > 0) : ?>
                            <?php foreach ($top as $key => $score) : ?>
                                <tr>

                                    <td><?php se($key + 1); ?></td>
                                    <td><a href="profile.php?id=<?php se($score, "user_id"); ?>"><?php se($score, "username"); ?></a></td>
                                    <td><?php se($score, "max_score"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td>No recorded scores yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>