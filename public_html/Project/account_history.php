<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
//pagination

//appending a WHERE clause of true makes it easier to append dynamic filters/conditions

//query to get paginated data
$d_query = "SELECT p.created, p.point_change `change in balance`, p.reason, p.memo,  IF(a2.account = '0', 'System', a2.account) as `account`
 FROM Points_History p JOIN Accounts a1 on a1.id = p.account_src JOIN Accounts a2 on a2.id = p.account_dest WHERE 1";

//filters
$start = se($_GET, "start", date("Y-m-d", strtotime("-1 month")), false);
$end = se($_GET, "end", date("Y-m-d"), false);
$type = se($_GET, "type", false, false);
$params = [];
//note the spaces before the AND
if ($start) {
    //don't forget to prefix the ambiguous column name
    $d_query .= " AND p.created >= :start";
    $params[":start"] = $start;
}
if ($end) {
    //don't forget to prefix the ambiguous column name
    $d_query .= " AND p.created <= :end";
    //offset the time to be 1 minute before end of day
    //by default the time component is 00:00:00 which is the beginning if this day
    //$params[":end"] = $end;
    $params[":end"] = date("Y-m-d 23:59:59", strtotime($end));
}
if ($type && $type !== "none") {
    $d_query .= " AND reason = :type";
    $params[":type"] = $type;
}
$d_query .= " AND account_src = :src";
$params[":src"] = get_user_account_id();
//sort
//never feed this value directly into the query
//you'll want to apply mapping to ensure it's definitely a safe value
$sort = se($_GET, "sort", false, false);
switch ($sort) {
    case "+date":
        //depending on the query we don't really need to order the data for total
        //this query specifically doesn't require it but I inlcuded it anyway
        $d_query .= " ORDER BY p.created asc";
        break;
    case "-date":
        //depending on the query we don't really need to order the data for total
        //this query specifically doesn't require it but I inlcuded it anyway
        $d_query .= " ORDER BY p.created desc";
        break;
    case "+change":
        //depending on the query we don't really need to order the data for total
        //this query specifically doesn't require it but I inlcuded it anyway
        $d_query .= " ORDER BY point_change asc";
        break;
    case "-change":
        //depending on the query we don't really need to order the data for total
        //this query specifically doesn't require it but I inlcuded it anyway
        $d_query .= " ORDER BY point_change asc";
        break;
    default:
        //depending on the query we don't really need to order the data for total
        //this query specifically doesn't require it but I inlcuded it anyway
        $d_query .= " ORDER BY p.created desc ";
        break;
}

//paginate
$records_per_page = 10;
$results = paginate($d_query, $params, $records_per_page); //populates $total_records
//calc number of pages (this is used for the pagination links below)

$total_pages = ceil($total_records / $records_per_page);

//fetch the potential reasons for a point change; used for my dropdown filter
$options = [];
$query = "SELECT DISTINCT reason from Points_History";
$stmt = $db->prepare($query);
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $options = $r;
    }
} catch (PDOException $e) {
    error_log("Error getting unique reasons: " . var_export($e->errorInfo, true));
}
//var_dump($options);
?>
<div class="container-fluid">
    <?php $title = "Account History";
    include(__DIR__ . "/../../partials/title.php"); ?>
    <div>
        <form>
            <div class="input-group mb-3">
                <span class="input-group-text" id="start-date">Start</span>
                <input name="start" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="start date" aria-describedby="start-date" value="<?php se($start); ?>">
                <span class="input-group-text" id="end-date">End</span>
                <input name="end" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="end date" aria-describedby="end-date" value="<?php se($end); ?>">
                <span class="input-group-text" id="filter">Reason</span>
                <select class="form-control" name="type" aria-label="filter" aria-describedby="filter">
                    <option <?php if ($type === "none") {
                                echo "selected";
                            } ?> value="none">None</option>
                    <?php foreach ($options as $opt) : ?>
                        <?php /* $options isn't a flat array so $opt is an array of column name => value
                        Using array_values() to extract the values we want*/ ?>
                        <?php $v =  trim(array_values($opt)[0]); ?>
                        <option <?php if ($type === $v) {
                                    //used to show a preselected value by echoing the "selected" attribute if the condition is true
                                    //note this is inside the first <option > tag before the closing >
                                    echo "selected";
                                } ?> value="<?php se($v); ?>"><?php se(str_replace("_", " ", $v)); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="input-group-text" id="sort">Sort</span>
                <?php /* This is an example of a hardcoded dropdown. These values are not dynamic. */ ?>
                <select class="form-control" name="sort" aria-label="sort" aria-describedby="sort">
                    <option value="-date">Created New to Old</option>
                    <option value="+date">Created Old to New</option>
                    <option value="-change">Change High to Low</option>
                    <option value="+change">Change Low to High</option>
                </select>
            </div>
            <input type="submit" class="btn btn-primary" value="Filter" />
        </form>
    </div>
    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
    <?php else : ?>
        <?php /* Since I'm just showing data, I'm lazily using my dynamic view example */ ?>
        <?php include(__DIR__ . "/../../partials/dynamic_list.php"); ?>
    <?php endif; ?>
    <div>
        <?php /** required $total_pages and $page to be set */ ?>
        <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>