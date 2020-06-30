<?php
$search = "";
if(isset($_POST["search"])){
    $search = $_POST["search"];
}
?>
<form method="POST">
    <input type="text" name="search" placeholder="Search for Thing"
    value="<?php echo $search;?>"/>
    <select name="col">
        <option value="name">Name</option>
        <option value="quantity">Quantity</option>
        <option value="created">Created</option>
        <option value="modified">Modified</option>
    </select>
    <select name="order">
        <option value="1">Asc</option>
        <option value="0">Desc</option>
    </select>
    <input type="submit" value="Search"/>
</form>
<?php
if(isset($search)) {

    require("common.inc.php");
    try {
        //this is ok since we're in a try/catch block
        $order = $_POST["order"];
        $col = $_POST["col"];
        echo var_dump($order);
        //Potential Solutions
        //https://stackoverflow.com/questions/2542410/how-do-i-set-order-by-params-using-prepared-pdo-statement
        $query = "SELECT * FROM Things where name like CONCAT('%', :thing, '%') ORDER BY :col";
        if((int)$order == 1){
            $query .= " ASC";
        }
        else{
            $query .= " DESC";
        }
        echo $query;
        $stmt = getDB()->prepare($query);
        //Note: With a LIKE query, we must pass the % during the mapping
        $stmt->execute([":thing"=>$search, ":col"=>$col]);
        echo var_export($stmt->errorInfo());
        //Note the fetchAll(), we need to use it over fetch() if we expect >1 record
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
<!--This part will introduce us to PHP templating,
note the structure and the ":" -->
<!-- note how we must close each check we're doing as well-->
<?php if(isset($results) && count($results) > 0):?>
    <p>This shows when we have results</p>
    <ul>
        <!-- Here we'll loop over all our results and reuse a specific template for each iteration,
        we're also using our helper function to safely return a value based on our key/column name.-->
        <?php foreach($results as $row):?>
            <li>
                <?php echo get($row, "name")?>
                <?php echo get($row, "quantity");?>
                <a href="delete.php?thingId=<?php echo get($row, "id");?>">Delete</a>
            </li>
        <?php endforeach;?>
    </ul>
<?php else:?>
    <p>This shows when we don't have results</p>
<?php endif;?>
