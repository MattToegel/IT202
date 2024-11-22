<?php

/**
 * Inserts a single record or multiple records into a specified database table.
 * Throws exceptions for invalid input or database errors.
 * Important: Intentionally doesn't support JSON fields, only basic data types.
 *
 * @param string $table_name The sanitized name of the database table.
 * @param array $data An associative array for a single record or an array of associative arrays for multiple records.
 * @param array $opts Options including 'debug' flag, 'update_duplicate', and 'columns_to_update'.
 * @return array The last insert ID and number of rows affected for insert.
 * @throws InvalidArgumentException If input data is not valid.
 * @throws Exception For database-related errors.
 * 
 * @author Matt Toegel
 * @version 0.2 04/17/2024
 */
function insert($table_name, $data, $opts = ["debug" => false, "update_duplicate" => false, "columns_to_update" => []])
{
    if (!is_array($data)) {
        throw new InvalidArgumentException("Data must be an array");
    }
    if (empty($data)) {
        throw new InvalidArgumentException("Data cannot be empty");
    }
    if (empty($table_name)) {
        throw new InvalidArgumentException("Table name cannot be empty");
    }
    if (!is_string($table_name)) {
        throw new InvalidArgumentException("Table name must be a string");
    }

    $is_debug = isset($opts["debug"]) && $opts["debug"];
    $update_duplicate = isset($opts["update_duplicate"]) && $opts["update_duplicate"];
    $columns_to_update = isset($opts["columns_to_update"]) ? $opts["columns_to_update"] : [];
    $sanitized_table_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $table_name);
    $is_indexed = array_keys($data) === range(0, count($data) - 1);

    // Check data structure before proceeding
    if ($is_indexed) {
        foreach ($data as $index => $entity) {
            if (!is_array($entity)) {
                throw new Exception("Each item in the data array must be an associative array when using bulk insert.");
            }
            if (array_keys($entity) === range(0, count($entity) - 1)) {
                throw new Exception("Nested array of data cannot be an indexed array");
            }
        }
    } else {
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new Exception("The keys of the nested associative array must be strings");
            }
            if (is_array($value) || is_object($value)) {
                throw new Exception("The values of the nested associative array must be basic data types (not arrays or objects)");
            }
        }
    }

    // Sort keys and prepare columns and values clause
    $firstItem = $is_indexed ? $data[0] : $data;
    $sortedKeys = array_keys($firstItem);
    sort($sortedKeys); // Sort keys to ensure consistency
    // uncomment to fix issues when using column names that are reserved keywords
    //$sortedKeys = array_map(fn ($key) => "`$key`", $sortedKeys);
    $columns = join(", ", $sortedKeys);
    
    $valuesClause = [];
    $updateClause = [];

    if ($is_indexed) {
        foreach ($data as $index => $entity) {
            ksort($entity); // Sort array by key to match column order
            $placeholders = join(", ", array_map(fn ($key) => ":{$key}_{$index}", array_keys($entity)));
            $valuesClause[] = "($placeholders)";
        }
    } else {
        ksort($data); // Sort array by key to ensure correct order
        $placeholders = join(", ", array_map(fn ($key) => ":$key", array_keys($data)));
        $valuesClause[] = "($placeholders)";
    }

    $query = "INSERT INTO `$sanitized_table_name` ($columns) VALUES " . join(", ", $valuesClause);

    if ($update_duplicate) {
        if (empty($columns_to_update)) {
            $columns_to_update = $sortedKeys; // Use sorted keys if no specific columns provided
        }
        foreach ($columns_to_update as $column) {
            $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            $updateClause[] = "`$column`=VALUES(`$column`)";
        }
        $query .= " ON DUPLICATE KEY UPDATE " . join(", ", $updateClause);
    }

    $db = getDB(); // Assume getDB is a function that returns your PDO instance
    $stmt = $db->prepare($query);
    if ($is_debug) {
        error_log("Query: " . $query);
    }

    try {
        if ($is_indexed) {
            foreach ($data as $index => $entity) {
                foreach ($entity as $key => $value) {
                    $stmt->bindValue(":{$key}_{$index}", $value);
                    if ($is_debug) {
                        error_log("Binding value for :{$key}_{$index}: $value");
                    }
                }
            }
        } else {
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
                if ($is_debug) {
                    error_log("Binding value for :$key: $value");
                }
            }
        }
        $stmt->execute();
        return ["rowCount" => $stmt->rowCount(), "lastInsertId" => $db->lastInsertId()];
    } catch (PDOException $e) {
        throw $e;
    } catch (Exception $e) {
        throw $e;
    }
}
