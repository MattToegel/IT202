<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id,name,state,base_rate,mod_min,mod_max,next_stage_time, user_id from F20_Eggs WHERE name like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Name:</div>
                        <div><?php safer_echo($r["name"]); ?></div>
                    </div>
                    <div>
                        <div>State:</div>
                        <div><?php getState($r["state"]); ?></div>
                    </div>
                    <div>
                        <div>Next Stage:</div>
                        <div><?php safer_echo($r["next_stage_time"]); ?></div>
                    </div>
                    <div>
                        <div>Owner Id:</div>
                        <div><?php safer_echo($r["user_id"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="test_edit_egg.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="test_view_egg.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
