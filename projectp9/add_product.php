<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have access to visit this page");
    die(header("Location: login.php"));
}
?>
<?php
    if(isset($_POST["name"])){
        $name = safe_get($_POST, "name", "");
        $quantity = (int)safe_get($_POST, "quantity", 0);
        $description = safe_get($_POST, "description", "");
        $price = (int)safe_get($_POST, "price", 0);

        if(!empty($name) && !empty($description) && $quantity > 0 && $price > 0){
            $db = getDB();
            $query = "INSERT INTO tfp_products (name, description, quantity, price, user_id) VALUES (:t, :d, :q, :p, :u)";
            $stmt = $db->prepare($query);
            $r = $stmt->execute([
                ":t"=>$name,
                ":d"=>$description,
                ":q"=>$quantity,
                ":p"=>$price,
                ":u"=>get_user_id()
            ]);
            if($r){
                flash("Added item to Products Table");
            }
            else{
                flash("Error adding item to Products Table: " . var_export($stmt->errorInfo(), true));
            }
        
        }
    }

?>
<div class="container-fluid">
    <div class="h3">Add Product</div>
    <form method="POST">
        <div>
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" required />
        </div>
        <div>
            <label for="q" class="form-label">Quantity/Stock</label>
            <input type="number" name="quantity" id="q" class="form-control" required />
        </div>
        <div>
            <label for="p" class="form-label">Price</label>
            <input type="number" name="price" id="p" class="form-control" required />
        </div>
        <div>
            <label for="d" class="form-label">Description</label>
            <textarea name="description" id="d" class="form-control" required></textarea>
        </div>
        <div class="d-grid gap-2">
            <input type="submit" class="btn btn-success" value="Add Product" />
        </div>
    </form>
</div>

<?php require(__DIR__ . "/partials/flash.php");?>