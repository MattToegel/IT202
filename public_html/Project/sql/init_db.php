<h1>Database Helper Tool</h1>
<details>
    <summary>Info (About the tool)</summary>
    <p>The scope of this tool is to help us separate our structural queries into separate files for better organization.</p>
    <p>This tools job is to attempt to read all of those files and determine which ones are needed to run against your database to synchronize the structure.</p>
    <p>This tool only works for queries that take zero parameters.</p>
    <p>It can be used to preload some data via inserts, but those queries <em>MUST</em> but crafted in such a way that you don't generate duplicates during each run.</p>
    <p>Files should be <a href="https://en.wikipedia.org/wiki/Idempotence">Idempotent</a></p>
</details>
<br><br>
<?php
# IMPORTANT: There should be no need to edit anything in this file
# Simply drop new structural .sql files into this directory then access this file in the browser
# It'll load all of the sql files and attempt to run them against the database
# Make sure you prefix the file names with at least a left padded 3 digit incrementing number (i.e., 001, 002)
# This ensures the files always run in the precise order required (order of execution is extremely important)



#turn error reporting on
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//pull in db.php so we can access the variables from it
require_once(__DIR__ . "/../../../lib/db.php");
$count = 0;
try {
    //name each sql file in a way that it'll sort correctly to run in the correct order
    //my samples prefix filenames with #_
    /***
     * Finds all .sql files in structure directory.
     * Generates an array keyed by filename and valued as raw SQL from file
     * The .sql is important here since this directory may have other files types
     */
    foreach (glob(__DIR__ . "/*.sql") as $filename) {
        $sql[$filename] = file_get_contents($filename);
    }

    if (isset($sql) && $sql && count($sql) > 0) {
        echo "<p>Found " . count($sql) . " files...</p>";
        /***
         * Sort the array so queries are executed in anticipated order.
         * Be careful with naming, 1-9 is fine, 1-10 has 10 run after #1 due to string sorting.
         */
        ksort($sql);
        //connect to DB
        $db = getDB();
        /***
         * Let's make this function a bit smarter to save DB calls for small dev plans (i.e., heroku)
         */
        $stmt = $db->prepare("show tables");
        $stmt->execute();
        $count++;
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $t = [];
        //convert it to a flat array
        foreach ($tables as $row) {
            foreach ($row as $key => $value) {
                array_push($t, $value);
            }
        }
        foreach ($sql as $key => $value) {
?>
            <details>
                <summary><?php echo "Running: $key"; ?></summary>
                <pre><code><?php echo $value; ?></code></pre>
            </details>
            <?php
            $lines = explode("(", $value, 2);
            if (count($lines) > 0) {
                //these lines attempt to extract the table info from any create commands
                //to determine if they command should be skipped or not
                $line = $lines[0];
                //clear out duplicate whitespace
                $line = preg_replace('!\s+!', ' ', $line);
                //remove create table command
                $line = str_ireplace("create table", "", $line);
                //remove if not exists command
                $line = str_ireplace("if not exists", "", $line);
                //remove backticks
                $line = str_ireplace("`", "", $line);
                //trim whitespace in front and back
                $line = trim($line);
                if (in_array($line, $t)) {
                    echo "<p style=\"margin-left: 3em\">Blocked from running, table found in 'show tables' results. [This is ok, it reduces redundant DB calls]</p><br>";
                    continue;
                }
            }
            $stmt = $db->prepare($value);
            try {
                $result = $stmt->execute();
            } catch (PDOException $e) {
                //ignoring as we know it'll be an error
                //had to wrap in try catch due to PHP 8.0 now throwing errors for PDO exceptions
            }
            $count++;
            $error = $stmt->errorInfo();
            ?>
            <details style="margin-left: 3em">
                <summary>Status: <?php echo ($error[0] === "00000" ? "Success" : "Error"); ?></summary>
                <pre><?php echo var_export($error, true); ?></pre>
            </details>
            <br>
<?php
        }
        echo "<p>Init complete, used approximately $count db calls.</p>";
    } else {
        echo "<p>Didn't find any files, please check the directory/directory contents/permissions (note files must end in .sql)</p>";
    }
    $db = null;
} catch (Exception $e) {
    echo $e->getMessage();
    exit("Something went wrong");
}
