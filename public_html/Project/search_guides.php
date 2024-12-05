<?php
require_once(__DIR__ . "/../../partials/nav.php");

//search before query
$title = se($_GET, "title", "", false);
$topic = se($_GET, "topic", "", false);
$provider = se($_GET, "provider", "", false);
$type = se($_GET, "type", "", false);

// column is what to sort on
$column = se($_GET, "column", "", false);
// order is ascending or descending
$order = se($_GET, "order", "", false);

// valid column options to avoid SQL injection
$columns = ["title", "publishedDateTime", "name", "type", "provider", "topic"];
// maps the list to the expected format of render_input with type select (i.e., "title"=>"title")
// renders as this: <option value="key">value</option>
$columnMap = array_map(function ($v) {
    return [$v => $v];
}, $columns);
// sanitize, pick a valid default
if (!in_array($column, $columns)) {
    $column = "title";
}
// pick a valid default
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc";
}
$params = [];
$assoc_check = "";
// Append the user_id for a join if the user is logged in
if (is_logged_in()) {
    // return a 1 or 0 based on whether or not this guide is watched by this user
    $assoc_check = " (SELECT IFNULL(count(1), 0) FROM SC_UserGuides WHERE user_id = :user_id and guide_id = SCG.id LIMIT 1) as is_watched,";
    $params[":user_id"] = get_user_id();
}


$sql = "SELECT SCG.id,title,excerpt, $assoc_check GROUP_CONCAT(DISTINCT SCT.name) AS topics, publishedDateTime, GROUP_CONCAT(DISTINCT SCP.name) AS providers, type FROM SC_Guides as SCG
JOIN SC_GuideImages as SCGI on SCGI.guide_id = SCG.id
JOIN SC_Images SCI on SCGI.image_id = SCI.id
JOIN SC_GuideProviders as SCGP on SCGP.guide_id = SCG.id
JOIN SC_Providers as SCP on SCGP.provider_id = SCP.id
JOIN SC_GuideTopics as SCGT on SCGT.guide_id = SCG.id
JOIN SC_Topics as SCT on SCGT.topic_id = SCT.id";
// the first space is important
$where = " WHERE 1=1"; //default truthy WHERE clause


if (!empty($title)) {
    $where .= " AND title like :title";
    $params[":title"] = "%$title%";
}
if (!empty($topic) && $topic != "-1") {
    $where .= " AND SCT.id = :topic";
    $params[":topic"] = $topic;
}
if (!empty($provider) && $provider != "-1") {
    $where .= " AND SCP.id = :provider";
    $params[":provider"] = $provider;
}
if (!empty($type) && $type != "-1") {
    $where .= " AND type = :type";
    $params[":type"] = $type;
}
$limit = 10;
if (isset($_GET["limit"]) && !is_nan($_GET["limit"])) {
    $limit = (int)$_GET["limit"];
    if ($limit < 0 || $limit > 100) {
        $limit = 10;
    }
}
$sql .= $where;
$sql .= " GROUP BY SCG.id";
$sql .= " ORDER BY $column $order";
// pagination logic
$page = (int)se($_GET, "page", 1, false);
if ($page < 1) {
    $page = 1;
}
// calculate offset
$offset = ($page - 1) * $limit;
$sql .= " LIMIT $offset, $limit";
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
// get total possible values based on filters
// JOINS also filter (in addition to the WHERE clause)
$total = 0;

$sql = "SELECT COUNT(DISTINCT SCG.id) as c
FROM SC_Guides as SCG 
JOIN SC_GuideProviders as SCGP on SCGP.guide_id = SCG.id 
JOIN SC_Providers as SCP on SCGP.provider_id = SCP.id 
JOIN SC_GuideTopics as SCGT on SCGT.guide_id = SCG.id 
JOIN SC_Topics as SCT on SCGT.topic_id = SCT.id  
$where";
try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    if (isset($params[":user_id"])) {
        unset($params[":user_id"]);
    }
    $stmt->execute($params);
    $r = $stmt->fetch();
    if ($r) {
        $total = (int)$r["c"]; // called my virtual/temp column "c" for count
    }
} catch (PDOException $e) {
    flash("Error fetching count", "danger");
    error_log("Error fetching count: " . var_export($e, true));
    error_log("Query: $sql");
    error_log("Params: " . var_export($params, true));
}

$topics = get_topics(); //used for filter dropdown
$providers = get_providers(); // used for filter dropdown
$types = get_types(); // used for filter dropdown

//error_log("Topics: " . var_export($topics, true));
//error_log("Types: " . var_export($types, true));

// since I'm using cards and I didn't make a flexible "manager" like render_table()
// I need to transform my data
$results = array_map(function ($item) {
    if (!isset($item["id"])) {
        error_log("Missing result item id during mapping");
    }
    $id = se($item, "id", -1, false);
    $cleaned_get = $_GET;
    if (isset($_GET["id"])) {
        unset($_GET["id"]);
    }
    $item["delete_url"] = get_url("delete_guide.php?id=$id&") . http_build_query($cleaned_get);
    $item["view_url"] = get_url("view_guide.php?id=$id&") . http_build_query($cleaned_get);
    return $item;
}, $results);

// old render_table logic
/*$ignore_columns = ["id", "created", "modified", "guide_id", "image_id", "width", "height", "provider_id", "topic_id", "is_watched"];
$table = [
    "data" => $results,
    "title" => "Guides",
    "ignored_columns" => $ignore_columns,
    "delete_url" => get_url("delete_guide.php"),
    "view_url" => get_url("view_guide.php")
];*/
//error_log("Guides: " . var_export($results, true));
?>

<div class="container-fluid">
    <h5>Guide Search</h5>
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
    <div class="row">
        <div class="col">
            Results <?php echo count($results) . "/" . $total; ?>
        </div>
    </div>
    <div class="row">
        <?php foreach ($results as $guide): ?>
            <div class="col-3">
                <?php guide_card($guide); ?>
            </div>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
            No records to show
        <?php endif; ?>
    </div>
    <?php /* Old render table 'or' grid logic
    <?php if (isset($_GET["grid"])): ?>
        <div class="row">
            <?php foreach ($results as $guide): ?>
                <div class="col-3">
                    <?php guide_card($guide); ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($results)): ?>
                No records to show
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php render_table($table); ?>
    <?php endif; ?>
    */ ?>
    <div class="row">
        <?php include(__DIR__ . "/../../partials/pagination_nav.php"); ?>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>
