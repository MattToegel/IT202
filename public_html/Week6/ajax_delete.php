<?php
$result = array("status"=>200, "message"=>"Nothing happened");
if (isset($_GET["thingId"]) && !empty($_GET["thingId"])){
    if(is_numeric($_GET["thingId"])){
        $thingId = (int)$_GET["thingId"];
        $query = file_get_contents(__DIR__ . "/queries/DELETE_ONE_TABLE_THINGS.sql");
        if(isset($query) && !empty($query)) {
            require("common.inc.php");
            $stmt = getDB()->prepare($query);
            $stmt->execute([":id"=>$thingId]);
            $e = $stmt->errorInfo();
                if($e[0] == "00000"){
                    //we're just going to redirect back to the list
                    //it'll reflect the delete on reload
                    //also wrap it in a die() to prevent the script from any continued execution
                    $result["message"] = "Successfully deleted thing";
                }
                else{
                    $result["message"] = "Error deleting thing";
                    $result["error"] = var_export($e,true);
                    $result["status"] = 400;
            }
        }
    }
}
else{
    $result["message"] = "Invalid thing to delete";
    $result["status"] = 400;
}
echo json_encode($result);
?>