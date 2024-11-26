<?php
require_once(__DIR__ . "/../../partials/nav.php");

//search before query
$title = se($_GET, "title", "", false);
$topic = se($_GET, "topic", "", false);
$provider = se($_GET, "provider", "", false);
$type = se($_GET, "type", "", false);

$column = se($_GET, "column", "", false);
$order = se($_GET, "order", "", false);

$columns = ["title", "publishedDateTime", "name", "type", "provider", "topic"];
$columnMap = array_map(function ($v) {
    return [$v => $v];
}, $columns);
// sanitize
if (!in_array($column, $columns)) {
    $column = "title";
}
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc";
}

$sql = "SELECT SCG.id,title,excerpt, GROUP_CONCAT(DISTINCT SCT.name) AS topics, publishedDateTime, GROUP_CONCAT(DISTINCT SCP.name) AS providers, type FROM SC_Guides as SCG
JOIN SC_GuideImages as SCGI on SCGI.guide_id = SCG.id
JOIN SC_Images SCI on SCGI.image_id = SCI.id
JOIN SC_GuideProviders as SCGP on SCGP.guide_id = SCG.id
JOIN SC_Providers as SCP on SCGP.provider_id = SCP.id
JOIN SC_GuideTopics as SCGT on SCGT.guide_id = SCG.id
JOIN SC_Topics as SCT on SCGT.topic_id = SCT.id";
$params = [];
if (!empty($title)) {
    $sql .= " AND title like :title";
    $params[":title"] = "%$title%";
}
if (!empty($topic) && $topic != "-1") {
    $sql .= " AND SCT.id = :topic";
    $params[":topic"] = $topic;
}
if (!empty($provider) && $provider != "-1") {
    $sql .= " AND SCP.id = :provider";
    $params[":provider"] = $provider;
}
if (!empty($type) && $type != "-1") {
    $sql .= " AND type = :type";
    $params[":type"] = $type;
}
$limit = 10;
if (isset($_GET["limit"]) && !is_nan($_GET["limit"])) {
    $limit = (int)$_GET["limit"];
    if ($limit < 0 || $limit > 100) {
        $limit = 10;
    }
}
$sql .= " GROUP BY SCG.id";
$sql .= " ORDER BY $column $order";

$sql .= " LIMIT $limit";
$db = getDB();
$results = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params); //[":title" => "%$title%", ":type" => $type, ":domain"=>$provider, ":topic"=>$topic]);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (Exception $e) {
    error_log(var_export($e, true));
    error_log("fun happened");
    flash("Failed to fetch");
}

$topics = get_topics();
$providers = get_providers();
$types = get_types();

error_log("Topics: " . var_export($topics, true));
error_log("Types: " . var_export($types, true));
$ignore_columns = ["id", "created", "modified", "guide_id", "image_id", "width", "height", "provider_id", "topic_id"];
$table = [
    "data" => $results,
    "title" => "Guides",
    "ignored_columns" => $ignore_columns,
    "delete_url" => get_url("delete_guide.php"),
    "view_url" => get_url("view_guide.php")
];
error_log("Guides: " . var_export($results, true));
?>

<div class="container-fluid">
    <div>
        <form>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "title", "label" => "Title", "value" => $title]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "topic", "label" => "Topic", "value" => $topic, "type" => "select", "options" => $topics]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "type", "label" => "Type", "value" => $type, "type" => "select", "options" => $types]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "provider", "label" => "Providers", "value" => $provider, "type" => "select", "options" => $providers]); ?>
                </div>

            </div>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "column", "label" => "Sort", "value" => $column, "type" => "select", "options" => $columnMap]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "order", "label" => "Order", "value" => $order, "type" => "select", "options" => [["asc" => "asc"], ["desc" => "desc"]]]); ?>
                </div>
                <div class="col">
                    <?php render_button(["text" => "Search", "type" => "submit"]); ?>
                </div>
                <div class="col">
                    <a href="?" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($_GET["grid"])): ?>
        <div class="row">
            <?php foreach ($results as $guide): ?>
                <div class="col-3">
                    <?php guide_card($guide); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php render_table($table); ?>
    <?php endif; ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>