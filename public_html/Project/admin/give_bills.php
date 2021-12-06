<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: $BASE_PATH" . "home.php"));
    redirect("home.php");
}

$users = [];
$search = "";
if (isset($_POST["search"])) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email from Users where username like :n limit 50");
    try {
        $search = se($_POST, "search", "", false);
        $stmt->execute([":n" => "%$search%"]);
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $users = $r;
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }
}

if (isset($_POST["user"]) && isset($_POST["bills"])) {
    $user = se($_POST, "user", 0, false);
    $bills = (int)se($_POST, "bills", 0, false);
    $db = getDB();
    $stmt = $db->prepare("SELECT id from BGD_Accounts where user_id = :uid");
    $account_id = -1;
    try {
        $stmt->execute([":uid" => $user]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $account_id = se($r, "id", -1, false);
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }
    if (change_bills($bills, "admin", -1, $account_id, "Received $bills bills")) {
        flash("Gave $bills bills to user id $user", "success");
    }
}
?>
<div class="container-fluid">
    <h1>Give Bills</h1>
    <form method="POST" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="input-group mb-3">
            <input class="form-control" type="search" name="search" placeholder="Username search" />
            <input class="btn btn-primary" type="submit" value="Search" />
        </div>
    </form>
    <form method="POST">
        <input type="hidden" name="search" value="<?php se($search); ?>" />
        <div class="mb-3">
            <label for="u" class="form-label">User</label>
            <select class="form-control" id="u" name="user">
                <option value="">-</option>
                <?php if (!$users) : ?>
                    <option value="">Search for Users First</option>
                <?php endif; ?>
                <?php foreach ($users as $u) : ?>
                    <option value="<?php se($u, "id"); ?>"><?php echo se($u, "username", "", false) . " - " . se($u, "email", "", false); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="p" class="form-label">Bills</label>
            <input type="number" class="form-control" id="p" name="bills" />
        </div>
        <input type="submit" class="btn btn-primary" value="Give" />
    </form>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/footer.php");
?>