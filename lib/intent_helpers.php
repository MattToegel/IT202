<?php

function get_intent_types()
{
    $query =  "SELECT id,label from CA_Intent_Types";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            return $r;
        }
    } catch (PDOException $e) {
        error_log("Error getting intent types: " . var_export($e, true));
    }
    return null;
}

function get_intent_statuses()
{
    $query =  "SELECT id,label from CA_Intent_Status";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            return $r;
        }
    } catch (PDOException $e) {
        error_log("Error getting intent statuses: " . var_export($e, true));
    }
    return null;
}
function create_intent($cat_id, $requestor_id, $processor_id, $type_reference, $requestor_notes = "", $processor_notes = "")
{
    error_log("Requestor: [$requestor_id]");
    if ($requestor_id < 1 || empty($requestor_id)) {
        if (!is_logged_in()) {
            flash("You must be logged in to do this action", "danger");
            error_log("User not logged in during create_intent");
            return null;
        }
    }
    $db = getDB();
    $db->beginTransaction(); // must manually commit or rollback for this session
    $action_status = [];
    $query = "
    SELECT 
    a.type_id, 
    a.intent_type, 
    b.cat_status, 
    c.intent_status
FROM
    (SELECT cit.id as type_id, cit.label as intent_type FROM CA_Intent_Types cit WHERE cit.id = :tid OR cit.label = :tid) a
LEFT JOIN
    (SELECT c.status as cat_status FROM CA_Cats c WHERE c.id = :cat_id) b ON 1=1
LEFT JOIN
    (SELECT cis.label as intent_status FROM CA_Intent_Status cis JOIN CA_Intents ci ON ci.status = cis.id WHERE cat_id = :cat_id ORDER BY cis.modified LIMIT 1) c ON 1";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":tid" => $type_reference, ":cat_id" => $cat_id]);
        $result = $stmt->fetch();
        if ($result) {
            $action_status = $result;
            error_log("Action result: " . var_export($action_status, true));
        }
    } catch (PDOException $e) {
        error_log("Error gettting action status info: " . var_export($e, true));
    }
    if (count($action_status) < 0) {
        $db->rollBack(); //rollback any changes
        return null;
    }
    $intent_type_id = (int)se($action_status, "type_id", -1, false);
    $intent_status = strtolower(se($action_status, "intent_status", "", false));
    $intent_type = strtolower(se($action_status, "intent_type", "", false));
    $cat_status = strtolower(se($action_status, "cat_status", "", false));
    $adoption_rules = ["available", "fostered"];
    $foster_rules = ["available"];
    $intent_rules = ["", "approved", "rejected"];
    if (!in_array($intent_status, $intent_rules)) {
        flash("There is already another action in process for this cat, please check back later", "warning");
        error_log("Intent rule triggered, intent status: $intent_status");
        return null;
    }
    if ($intent_type == "adopt") {
        if (!in_array($cat_status, $adoption_rules)) {
            flash("Sorry you can't adopt this cat at this time", "warning");
            error_log("Adoption rule triggered, invalid cat status: $cat_status");
            return null;
        }
    } else if ($intent_type == "foster") {
        if (!in_array($cat_status, $foster_rules)) {
            flash("Sorry you can't foster this cat at this time", "warning");
            error_log("Foster rules triggered, invalid cat status: $cat_status");
            return null;
        }
    }
    $query = "INSERT INTO CA_Intents (cat_id, requestor_id, processor_id, type, status, requestor_notes, processor_notes)
    VALUES (:cat_id, :requestor_id, :processor_id, :type, (SELECT id FROM CA_Intent_Status WHERE label = 'pending' LIMIT 1), :rn, :pn)";
    $stmt = $db->prepare($query);
    $intent_id = -1;
    try {
        $r = $stmt->execute([
            ":cat_id" => $cat_id, ":requestor_id" => $requestor_id, ":processor_id" => $processor_id, ":type" => $intent_type_id,
            ":rn" => $requestor_notes, ":pn" => $processor_notes
        ]);
        if ($r) {
            $intent_id = $db->lastInsertId();
        }
    } catch (PDOException $e) {
        flash("An error occurred, please try again", "danger");
        error_log("create_intent() error: " . var_export($e, true));
        $db->rollBack(); //rollback any changes
    }
    if ($intent_id > 0) {
        $query = "UPDATE CA_Cats set previous_status = status, status = 'pending process' WHERE id = :cid";
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":cid" => $cat_id]);
            $db->commit(); //confirm all the transaction steps
            return $intent_id;
        } catch (PDOException $e) {
            error_log("Error updating cat status: " . var_export($e, true));
            $db->rollBack(); //rollback any changes
        }
    }
    return null;
}

function search_intents($requestor_id = -1, $processor_id = -1)
{
    $db = getDB();
    $search = $_GET;
    $total_query = "SELECT count(1) as total FROM CA_Intents as ci JOIN CA_Intent_Status cis on cis.id = ci.status JOIN CA_Intent_Types as cit on cit.id = ci.type
JOIN CA_Cats as c on c.id = ci.cat_id LEFT JOIN Users as pu on pu.id = processor_id
WHERE 1=1";
    $query = "SELECT ci.id, ci.cat_id, requestor_id, c.name as cat_name, pu.username as processor_name, cis.label as intent_status, cit.label as intent_type, requestor_notes, processor_notes, ci.modified
FROM CA_Intents as ci JOIN CA_Intent_Status cis on cis.id = ci.status JOIN CA_Intent_Types as cit on cit.id = ci.type
JOIN CA_Cats as c on c.id = ci.cat_id LEFT JOIN Users as pu on pu.id = processor_id
WHERE 1=1
";
    if ($requestor_id > 0) {
        $search["requestor_id"] = $requestor_id;
    }
    if ($processor_id > 0) {
        $search["processor_id"] = $processor_id;
    }
    _build_intents_where_clause($filter_query, $params, $search);

    global $total;
    $total = (int)get_potential_total_records($total_query . $filter_query, $params);
    $limit = (int)se($search, "limit", 10, false);
    error_log("total records: $total");
    $page = (int)se($search, "page", "1", false);
    if ($limit > 0 && $limit <= 100 && $page > 0) {
        $offset = ($page - 1) * $limit;
        if (is_numeric($offset) && is_numeric($limit)) {
            $filter_query .= " LIMIT $offset, $limit"; //offset is how many records to skip, limit is up to how many records to return for the page
        }
    }
    $stmt = $db->prepare($query . $filter_query);
    bind_params($stmt, $params);
    try {
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
            return $results;
        }
    } catch (PDOException $e) {
        error_log("Error searching for intents: " . var_export($e, true));
    }

    return [];
}

function _build_intents_where_clause(&$query, &$params, $search)
{
    // Add conditions to the query based on the search parameters
    foreach ($search as $key => $value) {
        if ($value == 0 || !empty($value)) {
            switch ($key) {
                case 'type':
                    $params[":type"] = $value;
                    $query .= " AND intent_type = :type";
                    break;
                case 'status':
                    $params[":status"] = $value;
                    $query .= " AND intent_status = :status";
                    break;
                case "requestor_id":
                    $params[":requestor_id"] = $value;
                    $query .= " AND requestor_id = :requestor_id";
                    break;
                case "processor_id":
                    $params[":processor_id"] = $value;
                    $query .= " AND (processor_id = :processor_id || processor_id is null)";
                    break;
                case "id":
                    $params[":id"] = $value;
                    $query .= " AND ci.id = :id";
                    break;
            }
        }
    }
    // order by
    if (isset($search["column"]) && !empty($search["column"]) && isset($search["order"]) && !empty($search["order"])) {
        global $VALID_ORDER_COLUMNS;
        $col = $search["column"];
        $order = $search["order"];
        // prevent SQL injection by checking it against a hard coded list
        if (!in_array($col, $VALID_ORDER_COLUMNS)) {
            $col = "name";
        }
        if (!in_array($order, ["asc", "desc"])) {
            $order = "asc";
        }
        // special mapping to use table name prefix to resolve ambiguity error
        if (in_array($col, ["created", "modified"])) {
            $col = "c.$col";
        }
        $query .= " ORDER BY $col $order, processor_id asc"; //<-- be absolutely sure you trust these values; we can't bind certain parts of the query due to how the parameter mapping works
    }
}
