<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
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
    $stmt = $db->prepare("SELECT incu.id,incu.name,egg.name as egg, Users.username from F20_Incubators as incu JOIN Users on incu.user_id = Users.id LEFT JOIN F20_Eggs as egg on incu.egg_id = egg.id WHERE incu.name like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
?>
    <h3>List Incubators</h3>
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
                            <div>Egg:</div>
                            <div><?php safer_echo($r["egg"]); ?></div>
                        </div>
                        <div>
                            <div>Owner:</div>
                            <div><?php safer_echo($r["username"]); ?></div>
                        </div>
                        <div>
                            <a type="button" href="test_edit_incubator.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                            <a type="button" href="test_view_incubator.php?id=<?php safer_echo($r['id']); ?>">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
<?php require(__DIR__ . "/../partials/flash.php");