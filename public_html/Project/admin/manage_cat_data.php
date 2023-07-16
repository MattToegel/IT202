<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

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
        if (in_array($columns, ["id", "api_id", "urls"])) {
            continue;
        }
        if (array_key_exists($column, $breed)) {
            $record[$column] = $breed[$column];
            if (empty($record[$column])) {
                if (str_contains($mappings[$column], "int")) {
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
    //error_log("data: " . var_export($data, true));
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

function process_temperament($result)
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
    $temperament = [];
    foreach ($data as $breed) {
        $t = se($breed, "temperament", "", false);
        $breed_id = se($breed, "id", "", false);

        if (!empty($t) && !empty($breed_id)) {
            $temps = array_map('trim', explode(',', $t));
            //error_log("temps map: " . var_export($temps,true));
            if (count($temps) > 0) {
                if (!isset($temperament[$breed_id])) {
                    $temperament[$breed_id] = [];
                }
                $temperament[$breed_id] =  array_merge($temperament[$breed_id], $temps);
                //array_push($temperament[$breed_id], $temps);
            }
        }
    }
    // Flatten the array
    $flat_temperament = [];
    foreach ($temperament as $key => $value) {
        $flat_temperament = array_merge($flat_temperament, $value);
    }
    // Get unique values
    $unique_temperament = array_unique($flat_temperament);

    // Info vs INSERT IGNORE and ON DUPLICATE KEY UPDATE: https://stackoverflow.com/a/4920619
    $query = "INSERT IGNORE INTO CA_Temperaments (name) VALUES ";
    $values = [];
    $placeholders = [];
    $i = 0;
    foreach ($unique_temperament as $ut) {
        $placeholders[] = "(:temperament{$i})";
        $values["temperament{$i}"] = $ut;
        $i++;
    }
    $query .= implode(',', $placeholders);

    $db = getDB();
    $stmt = $db->prepare($query);
    foreach ($values as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error inserting temperament data: " . var_export($e, true));
    }

    $query = "INSERT IGNORE INTO CA_BreedTemperaments (breed_id, temperament_id) VALUES ";
    $values = [];
    $placeholders = [];
    $i = 0;
    foreach ($temperament as $breed_id => $temps) {
        foreach ($temps as $temp) {
            $placeholders[] = "((SELECT id from CA_Breeds WHERE api_id = :b$i LIMIT 1), (SELECT id from CA_Temperaments WHERE name = :name$i LIMIT 1))";
            $values[] = [":b$i" => $breed_id, ":name$i" => $temp];
            $i++;
        }
    }
    $query .= implode(',', $placeholders);
    error_log("query: " . $query);
    error_log("data: " . var_export($values, true));
    $stmt = $db->prepare($query);
    $i = 0;
    foreach ($values as $index => $val) {
        foreach ($val as $key => $v) {
            $stmt->bindValue("$key", $v);
        }
    }
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error inserting breed-temperament data: " . var_export($e, true));
    }
}
$action = se($_POST, "action", "", false);
if ($action) {
    switch ($action) {
        case "breeds":
            $result = get("https://api.thecatapi.com/v1/breeds", "CAT_API_KEY", ["limit" => 75, "page" => 0], false);
            process_breeds($result);
            process_temperament($result);
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
        <div class="col">
            <a class="btn btn-secondary" href="<?php get_url("admin/cat_profile.php", true); ?>">Create Cat Profile</a>
        </div>
        <div class="col">
            <a class="btn btn-secondary" href="<?php get_url("admin/list_cats.php", true); ?>">List Cats</a>
        </div>
    </div>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/footer.php");
?>