<?php
function search_cats()
{
    // Initialize variables
    global $search; //make search available outside of this function
    if (isset($search) && !empty($search)) {
        $search = array_merge($search, $_GET);
    } else {
        $search = $_GET;
    }
    $cats = [];
    $params = [];

    // Build the SQL query
    $query = _build_search_query($params, $search);

    // Prepare the SQL statement
    $db = getDB();
    $stmt = $db->prepare($query);

    // Bind parameters to the SQL statement
    bind_params($stmt, $params);
    error_log("search query: " . var_export($query, true));
    error_log("params: " . var_export($params, true));
    // Execute the SQL statement and fetch results
    try {
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result) {
            $cats = $result;
        }
    } catch (PDOException $e) {
        flash("An error occurred while searching for cats: " . $e->getMessage(), "warning");
        error_log("Cat Search Error: " . var_export($e, true));
    }

    return $cats;
}

function get_potential_total_records($query, $params)
{
    if (!str_contains($query, "total")) {
        throw new Exception(("This query expects a 'total' column to be fetched"));
    }
    $db = getDB();
    $stmt = $db->prepare($query);
    //temporarily remove mappings that don't exist for the total query
    // note this is ok as $params is passed by value in this case, not by reference
    $params = array_filter($params, function ($key) use ($query) {
        return str_contains($query, $key);
    }, ARRAY_FILTER_USE_KEY);
    bind_params($stmt, $params);
    $total = 0;
    try {
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result && isset($result["total"])) {
            $total = (int)$result["total"];
            error_log("Total potential records: $total");
        }
    } catch (PDOException $e) {
        error_log("Error fetching total count for query: " . var_export($e, true));
    }
    return $total;
}

function _build_cats_where_clause(&$query, &$params, $search)
{
    $params[":image_limit"] = 1;
    // Add conditions to the query based on the search parameters
    foreach ($search as $key => $value) {
        if ($value == 0 || !empty($value)) {
            switch ($key) {
                case 'breed_id':
                    $params[":breed_id"] = $value;
                    $query .= " AND c.breed_id = :breed_id";
                    break;
                case 'status':
                    $params[":status"] = $value;
                    $query .= " AND c.status = :status";
                    break;
                case 'sex':
                    $params[":sex"] = $value;
                    $query .= " AND sex = :sex";
                    break;
                case 'fixed':
                    $params[":fixed"] = $value;
                    $query .= " AND fixed = :fixed";
                    break;
                case 'age_min':
                case 'age_max':
                    $min = se($search, "age_min", "0", false);
                    $max = se($search, "age_max", "999", false);
                    if (empty($min)) {
                        $min = "0";
                    }
                    if (empty($max)) {
                        $max = "999";
                    }
                    $params[":age_min"] = $min;
                    $params[":age_max"] = $max;
                    $query .= " AND TIMESTAMPDIFF(YEAR, c.born, CURDATE()) BETWEEN :age_min AND :age_max ";
                    break;
                case 'name':
                    $params[":name"] = "%$value%"; //partial match
                    $query .= " AND c.name like :name";
                    break;
                case 'temperament':
                    $i = 0;
                    $keys = [];
                    foreach ($value as $t) {
                        if (empty($t)) { //ignore "any"
                            continue;
                        }
                        $params[":t$i"] = $t;
                        array_push($keys, ":t$i");
                        $i++;
                    }
                    if (count($keys) > 0) {
                        $keys = join(",", $keys);
                        $query .= " AND c.breed_id in (SELECT bt.breed_id FROM CA_Temperaments t JOIN CA_BreedTemperaments bt on t.id = bt.temperament_id WHERE t.id in ($keys))";
                    }
                    break;
                case "id":
                    $params[":id"] = $value;
                    $query .= " AND c.id = :id";
                    break;
                case "image_limit":
                    $params[":image_limit"] = (int)$value;
                    break;
                case "owner_id":
                    $params[":owner_id"] = $value;
                    $query .= " AND c.id IN (SELECT cat_id FROM CA_Cat_Owner WHERE owner_id = :owner_id)";
                    break;
                case "new":
                    $query .= " AND NOT EXISTS (SELECT cat_id FROM CA_Cat_Owner where cat_id = c.id)";
                    break;
            }
        }
    }

    if (!has_role("Admin")) {
        $query .= " AND status != 'unavailable'";
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
        $query .= " ORDER BY $col $order"; //<-- be absolutely sure you trust these values; we can't bind certain parts of the query due to how the parameter mapping works
    }
}
// Note: & tells php to pass by reference so any changes made to $params are reflected outside of the function
function _build_search_query(&$params, $search)
{
    $search_query = "SELECT 
            c.id, 
            c.breed_id,
            c.name, 
            c.description, 
            b.name as breed, 
            CASE 
                WHEN c.sex = 'm' THEN 'Male'
                WHEN c.sex = 'f' THEN 'Female'
                ELSE 'N/A'
            END as sex, 
            c.fixed, 
            TIMESTAMPDIFF(YEAR, c.born, CURDATE()) AS age, 
            c.status,
            (SELECT GROUP_CONCAT(url SEPARATOR ', ') FROM CA_CatImages as CI JOIN CA_Images I on I.id = CI.image_id WHERE CI.cat_id = c.id LIMIT :image_limit) as urls,
            (SELECT GROUP_CONCAT(t.name SEPARATOR ', ') FROM CA_Temperaments t JOIN CA_BreedTemperaments bt on t.id = bt.temperament_id WHERE bt.breed_id = c.breed_id LIMIT 1) as temperament,
            co.owner_id as owner_id,
            u.username as username,
            c.modified as last_updated
            FROM 
            CA_Cats as c JOIN CA_Breeds as b on c.breed_id = b.id
            LEFT JOIN CA_Cat_Owner co on co.cat_id = c.id
            LEFT JOIN Users u on co.owner_id = u.id
            WHERE 1=1";
    $total_query = "SELECT count(1) as total FROM CA_Cats as c JOIN CA_Breeds as b on c.breed_id = b.id WHERE 1=1";
    _build_cats_where_clause($filter_query, $params, $search);
    //added pagination (need limit and page to be in $search)
    // produces a $total value for use in UI

    global $total;
    $total = (int)get_potential_total_records($total_query . $filter_query, $params);
    //This is a rough sample for proper total records, my over-reuse requires me to take a closer look
    global $total_records;
    $total_records = (int)get_potential_total_records($total_query, []);
    $limit = (int)se($search, "limit", 10, false);
    error_log("total records: $total");
    $page = (int)se($search, "page", "1", false);
    if ($limit > 0 && $limit <= 100 && $page > 0) {
        $offset = ($page - 1) * $limit;
        if (is_numeric($offset) && is_numeric($limit)) {
            $filter_query .= " LIMIT $offset, $limit"; //offset is how many records to skip, limit is up to how many records to return for the page
        }
    }

    return $search_query . $filter_query;
}
/**
 * Dynamically binds parameters based on value data type
 */
function bind_params($stmt, $params)
{
    // Bind parameters to the SQL statement
    foreach ($params as $k => $v) {
        $type = PDO::PARAM_STR;
        if (is_null($v)) {
            $type = PDO::PARAM_NULL;
        } else if (is_numeric($v)) {
            $type = PDO::PARAM_INT;
        }
        $stmt->bindValue($k, $v, $type);
    }
}
