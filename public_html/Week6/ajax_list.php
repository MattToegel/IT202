<?php
require("common.inc.php");
$query = file_get_contents(__DIR__ . "/queries/SELECT_ALL_TABLE_THINGS.sql");
//prep our response object
$result = array("status"=>"200");
if(isset($query) && !empty($query)){
    try {
        $stmt = getDB()->prepare($query);
        //we don't need to pass any arguments since we're not filtering the results
        $stmt->execute();
        //Note the fetchAll(), we need to use it over fetch() if we expect >1 record
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //Note: Whatever is echo'd or sent to the output buffer will be what's returned to an ajax call
        //This is important if we're sending JSON we don't want any other garbage data otherwise json parsing will fail
        $result["status"] = 200;
        $result["results"] = $results;
    }
    catch (Exception $e){
        $result["status"] = 400;
        $result["error"] = $e->getMessage();
    }
}
echo json_encode($result);
?>