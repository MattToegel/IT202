<?php
$thingId = -1;
$result = array();
require("common.inc.php");
if(isset($_GET["thingId"]) && !empty($_GET["thingId"])){
    $thingId = $_GET["thingId"];
    $query = file_get_contents(__DIR__ . "/SELECT_ONE_TABLE_THINGS.sql");
    if(isset($query) && !empty($query)) {
        //Note: SQL File contains a "LIMIT 1" although it's not necessary since ID should be unique (i.e., one record)
        $stmt = getDB()->prepare($query);
        $stmt->execute([":id" => $thingId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    else{
        echo "Failed to find SELECT_ONE_TABLE_THINGS.sql file";
    }
}
else{
    echo "No thingId provided in url, don't forget this or sample won't work.";
}
?>
<script src="script.js"></script>
<!-- note although <script> tag "can" be self terminating some browsers require the
full closing tag-->
<form method="POST" onsubmit="">
    <label for="thing">Thing Name
        <!-- since the last assignment we added a required attribute to the form elements-->
        <input type="text" id="thing" name="name" value="<?php echo get($result, "name");?>" required />
    </label>
    <label for="q">Quantity
        <!-- We also added a minimum value for our number field-->
        <input type="number" id="q" name="quantity" value="<?php echo get($result, "quantity");?>" required min="0"/>
    </label>
    <input type="submit" name="updated" value="Update Thing"/>
</form>

<?php
if(isset($_POST["updated"])){
    $name = "";
    $quantity = -1;
    if(isset($_POST["name"]) && !empty($_POST["name"])){
        $name = $_POST["name"];
    }
    if(isset($_POST["quantity"]) && !empty($_POST["quantity"])){
        if(is_numeric($_POST["quantity"])){
            $quantity = (int)$_POST["quantity"];
        }
    }
    if(!empty($name) && $quantity > -1){
        try{
            $query = NULL;
            $query = file_get_contents(__DIR__ . "/UPDATE_TABLE_THINGS.sql");
            if(isset($query) && !empty($query)) {
                $stmt = getDB()->prepare($query);
                $result = $stmt->execute(array(
                    ":name" => $name,
                    ":quantity" => $quantity,
                    ":id" => $thingId
                ));
                $e = $stmt->errorInfo();
                if ($e[0] != "00000") {
                    echo var_export($e, true);
                } else {
                    if ($result) {
                        echo "Successfully updated thing: " . $name;
                    } else {
                        echo "Error updating record";
                    }
                }
            }
            else{
                echo "Failed to find UPDATE_TABLE_THINGS.sql file";
            }
        }
        catch (Exception $e){
            echo $e->getMessage();
        }
    }
    else{
        echo "Name and quantity must not be empty.";
    }
}
?>
