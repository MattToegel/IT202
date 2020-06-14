<form method="POST">
	<label for="thing">Thing Name
	<input type="text" id="thing" name="name" />
	</label>
	<label for="q">Quantity
	<input type="number" id="q" name="quantity" />
	</label>
	<input type="submit" name="created" value="Create Thing"/>
</form>

<?php
if(isset($_POST["created"])){
    $name = $_POST["name"];
    $quantity = $_POST["quantity"];
    if(!empty($name) && !empty($quantity)){
        require("config.php");
        $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
        try{
            $db = new PDO($connection_string, $dbuser, $dbpass);
            $stmt = $db->prepare("INSERT INTO Things (name, quantity) VALUES (:name, :quantity)");
            $result = $stmt->execute(array(
                ":name" => $name,
                ":quantity" => $quantity
            ));
            $e = $stmt->errorInfo();
            if($e[0] != "00000"){
                echo var_export($e, true);
            }
            else{
                echo var_export($result, true);
                if ($result){
                    echo "Successfully inserted new thing: " . $name;
                }
                else{
                    echo "Error inserting record";
                }
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