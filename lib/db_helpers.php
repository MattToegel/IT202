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


// delete below section if you get PDO errors, Intelliphense incorrectly referse to this mock
// used for testing via the cli (note: normally you'd used something like PHPUnit for proper test cases)
if (php_sapi_name() == "cli") {
    // Define the cli-only here
    class MockPDOStatement
    {
        public $queryString;

        public function __construct($queryString = '')
        {
            $this->queryString = $queryString;
        }

        public function bindValue($parameter, $value, $type = PDO::PARAM_STR)
        {
            // Optionally, print the bindValue calls for verification
            echo "bindValue called with: Parameter: $parameter, Value: $value, Type: $type\n";
        }

        public function execute()
        {
            // Simulate query execution
            echo "Execute called on query: $this->queryString\n";
            // Return true to simulate a successful execution
            return true;
        }

        public function rowCount()
        {
            // Return a simulated row count
            return 1;
        }

        public function fetch()
        {
            // Simulate fetching data
            return false; // Adjust based on needs
        }
    }

    class MockPDO
    {
        public function prepare($query)
        {
            echo "Prepare called with query: $query\n";
            // Return a new mock PDOStatement with the query
            return new MockPDOStatement($query);
        }

        public function lastInsertId()
        {
            // Simulate retrieving the last insert ID
            return 42; // Example ID
        }
    }
    function getDB()
    {
        // Return the mock PDO object instead of a real PDO object
        return new MockPDO();
    }
    // test suite
    function test_insert()
    {
        echo "Test 1: Expect pass " . PHP_EOL;
        try {
            insert('users', ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']);
            echo 'Test 1 Passed: Valid single record inserted successfully.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 1 Failed: ' . $e->getMessage() . PHP_EOL;
        }
        echo "Test 2: Expect pass " . PHP_EOL;
        try {
            insert('users', [
                ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
                ['id' => 3, 'name' => 'Alice', 'email' => 'alice@example.com']
            ]);
            echo 'Test 2 Passed: Valid multiple records inserted successfully.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 2 Failed: ' . $e->getMessage() . PHP_EOL;
        }
        echo "Test 3: Expect fail " . PHP_EOL;
        try {
            insert('users', [
                'id', 'name', 'email'
            ]); // Incorrect structure
            echo 'Test 3 Passed: Incorrect data type handled.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 3 Failed: ' . $e->getMessage() . PHP_EOL;
        }
        echo "Test 4: Expect fail " . PHP_EOL;
        try {
            insert('users', []);
            echo 'Test 4 Passed: Empty array handled.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 4 Failed: ' . $e->getMessage() . PHP_EOL;
        }
        echo "Test 5: Expect fail " . PHP_EOL;
        try {
            insert(['users'], [
                'id' => 4, 'name' => 'Bob', 'email' => 'bob@example.com'
            ]);
            echo 'Test 5 Passed: Invalid table name type handled.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 5 Failed: ' . $e->getMessage() . PHP_EOL;
        }
        echo "Test 6: Expect fail " . PHP_EOL;
        try {
            insert('users', [[1, 2, 3], [4, 5, 6]]); // Incorrect batch insert data
            echo 'Test 6 Passed: Non-associative array in batch insert handled.' . PHP_EOL;
        } catch (Exception $e) {
            echo 'Test 6 Failed: ' . $e->getMessage() . PHP_EOL;
        }
    }
    // Call the function to execute
    test_insert();
}
