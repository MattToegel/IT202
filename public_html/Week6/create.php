<script src="js/script.js"></script>
<!-- note although <script> tag "can" be self terminating some browsers require the
full closing tag-->
<form method="POST" onsubmit="return validate(this);">
    <label for="thing">Thing Name
        <input type="text" id="thing" name="name" required />
    </label>
    <label for="q">Quantity
        <input type="number" id="q" name="quantity" required min="0" />
    </label>
    <input type="submit" name="created" value="Create Thing"/>
</form>
<?php
if(isset($_POST["created"])) {
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
    //If name or quantity is invalid, don't do the DB part
    if(empty($name) || $quantity < 0 ){
        echo "Name must not be empty and quantity must be greater than or equal to 0";
        die();//terminates the rest of the script
    }
    try {
        require("common.inc.php");
        $query = file_get_contents(__DIR__ . "/queries/INSERT_TABLE_THINGS.sql");
        if(isset($query) && !empty($query)) {
            $stmt = getDB()->prepare($query);
            $result = $stmt->execute(array(
                ":name" => $name,
                ":quantity" => $quantity
            ));
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                echo var_export($e, true);
            } else {
                if ($result) {
                    echo "Successfully inserted new thing: " . $name;
                } else {
                    echo "Error inserting record";
                }
            }
        }
        else{
            echo "Failed to find INSERT_TABLE_THINGS.sql file";
        }
    }
    catch (Exception $e){
        echo $e->getMessage();
    }
}
?>