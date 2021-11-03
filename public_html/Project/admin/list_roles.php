<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "home.php"));
}
//handle the toggle first so select pulls fresh data
if (isset($_POST["role_id"])) {
    $role_id = se($_POST, "role_id", "", false);
    if (!empty($role_id)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Roles SET is_active = !is_active WHERE id = :rid");
        try {
            $stmt->execute([":rid" => $role_id]);
            flash("Updated Role", "success");
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}
$query = "SELECT id, name, description, is_active from Roles";
$params = null;
if (isset($_POST["role"])) {
    $search = se($_POST, "role", "", false);
    $query .= " WHERE name LIKE :role";
    $params =  [":role" => "%$search%"];
}
$query .= " ORDER BY modified desc LIMIT 10";
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $roles = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

?>
<div class="container-fluid">
    <h1>List Roles</h1>
    <form method="POST" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="input-group mb-3">
            <input class="form-control" type="search" name="role" placeholder="Role Filter" />
            <input class="btn btn-primary" type="submit" value="Search" />
        </div>
    </form>
    <table class="table text-light">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Active</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php if (empty($roles)) : ?>
                <tr>
                    <td colspan="100%">No roles</td>
                </tr>
            <?php else : ?>
                <?php foreach ($roles as $role) : ?>
                    <tr>
                        <td><?php se($role, "id"); ?></td>
                        <td><?php se($role, "name"); ?></td>
                        <td><?php se($role, "description"); ?></td>
                        <td><?php echo (se($role, "is_active", 0, false) ? "active" : "disabled"); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="role_id" value="<?php se($role, 'id'); ?>" />
                                <?php if (isset($search) && !empty($search)) : ?>
                                    <?php /* if this is part of a search, lets persist the search criteria so it reloads correctly*/ ?>
                                    <input type="hidden" name="role" value="<?php se($search, null); ?>" />
                                <?php endif; ?>
                                <input class="btn btn-warning" type="submit" value="Toggle" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    //note we need to go up 1 more directory
    require_once(__DIR__ . "/../../../partials/flash.php");
    ?>