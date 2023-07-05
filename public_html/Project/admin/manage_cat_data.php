<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
//TODO need to update insert_breeds... to use the $mappings array and not go based on is_int for value
function insert_breeds_into_db($db, $breeds, $mappings)
{
    // Prepare SQL query
    $query = "INSERT INTO `CA_Breeds` ";
    if (count($breeds) > 0) {
        $cols = array_keys($breeds[0]);
        $query .= "(" . implode(",", array_map(function ($col) {
            return "`$col`";
        }, $cols)) . ") VALUES ";

        // Generate the VALUES clause for each breed
        $values = [];
        foreach ($breeds as $i => $breed) {
            $breedPlaceholders = array_map(function ($v) use ($i) {
                return ":" . $v . $i;  // Append the index to make each placeholder unique
            }, $cols);
            $values[] = "(" . implode(",", $breedPlaceholders) . ")";
        }

        $query .= implode(",", $values);

        // Generate the ON DUPLICATE KEY UPDATE clause
        $updates = array_reduce($cols, function ($carry, $col) {
            $carry[] = "`$col` = VALUES(`$col`)";
            return $carry;
        }, []);

        $query .= " ON DUPLICATE KEY UPDATE " . implode(",", $updates);

        // Prepare the statement
        $stmt = $db->prepare($query);

        // Bind the parameters for each breed
        foreach ($breeds as $i => $breed) {
            foreach ($cols as $col) {
                $placeholder = ":$col$i";
                $val = isset($breed[$col]) ? $breed[$col] : "";
                $param = PDO::PARAM_STR;
                if (str_contains($mappings[$col], "int")) {
                    $param = PDO::PARAM_INT;
                }
                $stmt->bindValue($placeholder, $val, $param);
            }
        }

        // Execute the statement
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log(var_export($e, true));
        }
    }
}

function process_single_breed($breed, $columns, $mappings)
{
    // Process breed data
    $weight = isset($breed["weight"]) ? se($breed["weight"], "imperial", "0-0", false) : "0-0";
    $life_span = se($breed, "life_span", "12-15", false);
    $weight = array_map('trim', explode('-', $weight));
    $life_span = array_map('trim', explode('-', $life_span));

    // Prepare record
    $record = [];
    $record["api_id"] = se($breed, "id", "", false);
    $record["min_weight_lbs"] = $weight[0];
    $record["max_weight_lbs"] = $weight[1];
    $record["min_life_span_years"] = $life_span[0];
    $record["max_life_span_years"] = $life_span[1];
    $record["urls"] = implode(",", array_intersect_key($breed, array_flip(["wikipedia_url", "vcahospitals_url", "vetstreet_url", "cfa_url"])));

    // Map breed data to columns
    foreach ($columns as $column) {
        if(in_array($columns, ["id", "api_id", "urls"])){
            continue;
        }
        if(array_key_exists($column, $breed)){
            $record[$column] = $breed[$column];
            if(empty($record[$column])){
                if(str_contains($mappings[$column], "int")){
                    $record[$column] = "0";
                }
            }
        }
    }
    error_log("Record: " . var_export($record, true));
    return $record;
}

function process_breeds($result)
{
    $status = se($result, "status", 400, false);
    if ($status != 200) {
        return;
    }

    // Extract data from result
    $data_string = html_entity_decode(se($result, "response", "{}", false));
    $wrapper = "{\"data\":$data_string}";
    $data = json_decode($wrapper, true);
    if (!isset($data["data"])) {
        return;
    }
    $data = $data["data"];
    error_log("data: " . var_export($data, true));
    // Get columns from CA_Breeds table
    $db = getDB();
    $stmt = $db->prepare("SHOW COLUMNS FROM CA_Breeds");
    $stmt->execute();
    $columnsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare columns and mappings
    $columns = array_column($columnsData, 'Field');
    $mappings = [];
    foreach ($columnsData as $column) {
        $mappings[$column['Field']] = $column['Type'];
    }
    $ignored = ["id", "created", "modified"];
    $columns = array_diff($columns, $ignored);

    // Process each breed
    $breeds = [];
    foreach ($data as $breed) {
        $record = process_single_breed($breed, $columns, $mappings);
        array_push($breeds, $record);
    }

    // Insert breeds into database
    insert_breeds_into_db($db, $breeds, $mappings);
}

$action = se($_POST, "action", "", false);
if ($action) {
    switch ($action) {
        case "breeds":
            $result = get("https://api.thecatapi.com/v1/breeds", "CAT_API_KEY", ["limit" => 75, "page" => 0], false);
            process_breeds($result);
            break;
    }
}
?>

<div class="container-fluid">
    <h1>Cat Data Management</h1>
    <div class="row">
        <div class="col">
            <!-- Breed refresh button -->
            <form method="POST">
                <input type="hidden" name="action" value="breeds" />
                <input type="submit" class="btn btn-primary" value="Refresh Breeds" />
            </form>
        </div>
    </div>
</div>