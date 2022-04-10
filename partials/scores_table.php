<?php
//requires functions.php
//requires a duration to be set
if (!isset($duration)) {
    $duration = "day"; //choosing to default to day
}

if (in_array($duration, ["day", "week", "month", "lifetime"])) {
    $results = get_top_10($duration);
} else if ($duration === "latest") {
    if (!isset($user_id)) {
        $user_id = get_user_id();
    }
    $results = get_latest_scores($user_id);
}
switch ($duration) {
    case "day":
        $title = "Top Scores Today";
        break;
    case "week":
        $title = "Top Scores This Week";
        break;
    case "month":
        $title = "Top Scores This Month";
        break;
    case "lifetime":
        $title = "All Time Top Scores";
        break;
    case "latest":
        $title = "Latest Scores";
        break;
    default:
        $title = "Invalid Scoreboard";
        break;
}
?>
<div class="card bg-light">
    <div class="card-body">
        <div class="card-title">
            <div class="fw-bold fs-3">
                <?php se($title); ?>
            </div>
        </div>
        <div class="card-text">
            <table class="table">
                <?php if (count($results) == 0) : ?>
                    <p>No results to show</p>
                <?php else : ?>
                    <?php foreach ($results as $index => $record) : ?>
                        <?php if ($index == 0) : ?>
                            <thead>
                                <?php foreach ($record as $column => $value) : ?>
                                    <th><?php se($column); ?></th>
                                <?php endforeach; ?>
                            </thead>
                        <?php endif; ?>
                        <tr>
                            <?php foreach ($record as $column => $value) : ?>
                                <td><?php se($value, null, "N/A"); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
            </table>
        <?php endif; ?>
        </table>
        </div>
    </div>
</div>