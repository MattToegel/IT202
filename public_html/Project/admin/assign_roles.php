<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "home.php"));
}
//attempt to apply
if (isset($_POST["users"]) && isset($_POST["roles"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $role_ids = $_POST["roles"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($role_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        //for sake of simplicity, this will be a tad inefficient
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserRoles (user_id, role_id, is_active) VALUES (:uid, :rid, 1) ON DUPLICATE KEY UPDATE is_active = !is_active");
        foreach ($user_ids as $uid) {
            foreach ($role_ids as $rid) {
                try {
                    $stmt->execute([":uid" => $uid, ":rid" => $rid]);
                    flash("Updated role", "success");
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }
    }
}

//get active roles
$active_roles = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, name, description FROM Roles WHERE is_active = 1 LIMIT 10");
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $active_roles = $results;
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

//search for user by username
$users = [];
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username, (SELECT GROUP_CONCAT(name, ' (' , IF(ur.is_active = 1,'active','inactive') , ')') from 
        UserRoles ur JOIN Roles on ur.role_id = Roles.id WHERE ur.user_id = Users.id) as roles
        from Users WHERE username like :username");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $users = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}


?>
<h1>Assign Roles</h1>
<form method="POST">
    <input type="search" name="username" placeholder="Username search" />
    <input type="submit" value="Search" />
</form>
<form method="POST">
    <?php if (isset($username) && !empty($username)) : ?>
        <input type="hidden" name="username" value="<?php se($username, false); ?>" />
    <?php endif; ?>
    <table>
        <thead>
            <th>Users</th>
            <th>Roles to Assign</th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <table>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td>
                                    <label for="user_<?php se($user, 'id'); ?>"><?php se($user, "username"); ?></label>
                                    <input id="user_<?php se($user, 'id'); ?>" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" />
                                </td>
                                <td><?php se($user, "roles", "No Roles"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
                <td>
                    <?php foreach ($active_roles as $role) : ?>
                        <div>
                            <label for="role_<?php se($role, 'id'); ?>"><?php se($role, "name"); ?></label>
                            <input id="role_<?php se($role, 'id'); ?>" type="checkbox" name="roles[]" value="<?php se($role, 'id'); ?>" />
                        </div>
                    <?php endforeach; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Toggle Roles" />
</form>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>