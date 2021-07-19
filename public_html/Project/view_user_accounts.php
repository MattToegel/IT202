<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
?>
<?php
$search = ""; //init to empty for prefill
$results = [];
if (isset($_POST["search"]) && !empty($_POST["search"])) {
    $search = se($_POST, "search", "", false);
    $db = getDB();
    //it's usually a good idea to apply a limit to query where there's no real know maximum, later on you'd paginate these excessive results
    $query = "SELECT a.id, account, balance, username, a.created, a.modified FROM Users u join Accounts a on u.id = a.user_id WHERE username like :user LIMIT 25";
    $stmt = $db->prepare($query);
    try {
        //we need to pass the wildcards here with the value, otherwise prepare will ignore them
        $stmt->execute([":user" => "%$search%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        flash("Unexpected error: " . var_export($e->errorInfo, true), "warning");
    }
}
?>
<div class="container-fluid">
    <h3>User Account Lookup</h3>
    <form method="post">
        <div><label class="form-label" for="search">Lookup Username</label>
            <input class="form-control" type="text" name="search" id="search" value="<?php se($search); ?>" />
        </div>
        <input class="btn btn-primary" type="submit" value="Search" />
    </form>
    <div>
        <?php if (!$results || count($results) == 0) : ?>
            <p>No results to show</p>
        <?php else : ?>
            <?php /* Since I'm just showing data, I'm lazily using my dynamic view example */ ?>
            <?php include(__DIR__ . "/../../partials/dynamic_list.php"); ?>
        <?php endif; ?>
    </div>
</div>