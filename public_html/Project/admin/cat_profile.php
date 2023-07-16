<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: " . get_url("home.php")));
    redirect("home.php");
}
/**
 * This page will handle both create and edit
 */
$breeds = [];
$statuses = ["Adopted", "Fostered", "Unavailable", "Available"];
$statuses = array_map(function ($v) {
    return ["label" => $v, "value" => strtolower($v)];
}, $statuses);

$result = get_breeds();
// convert breed data to render_input's expected "options" data
$breeds = array_map(function ($v) {
    return ["label" => $v["name"], "value" => $v["id"]];
}, $result);

$sex = [
    ["label" => "Male", "value" => "m"],
    ["label" => "Female", "value" => "f"]
];
$id = (int)se($_GET, "id", 0, false);
$cat = [];
if (count($_POST) > 0) {
    $cat = $_POST;
    $images = isset($_POST["images"]) ? $_POST["images"] : [];
    //note: need to convert checkbox fields differently, unchecked ones don't get submitted
    $_POST["fixed"] = isset($_POST["fixed"]) ? 1 : 0; // convert to boolean
    //remove the images key so it doesn't impact our helper save_data()/update_data()
    unset($_POST["images"]);
    $cat_id = -1;
    if (validate_cat($_POST)) {
        if ($id > 0) {
            if (update_data("CA_Cats", $id, $_POST, ["id"])) {
                $cat_id = $id;
            }
        } else {
            $cat_id = save_data("CA_Cats", $_POST);
        }
    }
    $image_step_passed = false;
    if ($cat_id > 0 && count($images) > 0) {
        $query = "INSERT INTO CA_CatImages(image_id, cat_id) VALUES ";
        $i = 0;
        foreach ($images as $img) {
            $placeholders[] = "(:img_id$i, :cat_id$i)";
            $values[] = [":img_id$i" => $img, ":cat_id$i" => $cat_id];
            $i++;
        }
        $query .= implode(",", $placeholders);
        $query .= " ON DUPLICATE KEY UPDATE is_active = !is_active";
        $db = getDB();
        $stmt = $db->prepare($query);

        foreach ($values as $val) {
            /*foreach ($val as $key => $v) {
                $stmt->bindValue($key, $v);
            }*/
            bind_params($stmt, $val);
        }
        try {
            $stmt->execute();
            $image_step_passed = true;
        } catch (PDOException $e) {
            error_log("Error inserting/updating cat image references: " . var_export($e, true));
            flash("There was an problem associating images", "danger");
        }
    }
    $has_images = count($images) > 0;
    $images_ok = $has_images && $image_step_passed;
    if ($cat_id > 0 && ($images_ok || !$has_images)) {
        flash("Successfully set profile for " . $cat["name"], "success");
        redirect("admin/cat_profile.php?id=$cat_id");
    }
}

if ($id > 0) {
    $db = getDB();

    $query = "SELECT name, breed_id, breed_extra, born, sex, fixed, weight, description, status,
    (SELECT GROUP_CONCAT(url) FROM CA_CatImages as CCI JOIN CA_Images as CI on CI.id = CCI.image_id WHERE CCI.cat_id = CC.id) as images 
    FROM CA_Cats as CC WHERE id = :id";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":id" => $id]);
        $result = $stmt->fetch();
        if ($result) {
            $cat = $result;
            if (isset($cat["images"])) {
                $cat["images"] = explode(",", $cat["images"]);
            }
            error_log("Cat result: " . var_export($cat, true));
        } else {
            flash("There was a problem finding this cat", "danger");
        }
    } catch (PDOException $e) {
        error_log("Error fetching cat by id: " . var_export($e, true));
        flash("An unhandled error occurred", "danger");
    }
}
$data = $_GET;
unset($data["id"]);
$back = "admin/list_cats.php?" . http_build_query($data);
?>
<div class="container-fluid">
    <h1>Cat Profile</h1>
    <a class="btn btn-secondary" href="<?php get_url($back, true); ?>">Back</a>
    <form method="POST">
        <?php render_input(["type" => "text", "id" => "name", "name" => "name", "label" => "Name", "rules" => ["minlength" => 2, "required" => true], "value" => se($cat, "name", "", false)]); ?>
        <?php render_input(["type" => "select", "id" => "status", "name" => "status", "label" => "Status", "options" => $statuses, "rules" => ["required" => true], "value" => se($cat, "status", "", false)]); ?>
        <?php render_input(["type" => "select", "id" => "breed", "name" => "breed_id", "label" => "Breed", "options" => $breeds, "rules" => ["required" => true], "value" => se($cat, "breed_id", "", false)]); ?>
        <?php render_input(["type" => "text", "id" => "breed_extra", "name" => "breed_extra", "label" => "Extra Breed Info (optional)", "value" => se($cat, "breed_extra", "", false)]); ?>
        <div>
            <?php render_button(["text" => "Fetch Images", "type" => "button", "onclick" => "fetch_images(event)"]); ?>
            <?php if (isset($cat["images"])) : ?>
                <div class="row">
                    <?php foreach ($cat["images"] as $img) : ?>
                        <img style="width:128px; height:128px" src="<?php se($img, "url"); ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div id="image_container" class="row"></div>
        </div>
        <?php render_input(["type" => "select", "id" => "sex", "name" => "sex", "label" => "Sex", "options" => $sex, "rules" => ["required" => true], "value" => se($cat, "sex", "", false)]); ?>
        <?php render_input(["type" => "switch", "id" => "fixed", "name" => "fixed", "label" => "Fixed (spayed/neutered)", "value" => se($cat, "fixed", "", false)]); ?>
        <?php render_input(["type" => "date", "id" => "born", "name" => "born", "label" => "Born",  "rules" => ["required" => true], "value" => se($cat, "born", date("Y-m-d"), false)]); ?>
        <?php render_input(["type" => "number", "id" => "weight", "name" => "weight", "label" => "Weight (lbs.)", "rules" => ["min" => 0, "required" => true], "value" => se($cat, "weight", "0", false)]); ?>
        <?php render_input(["type" => "textarea", "id" => "description", "name" => "description", "label" => "Description", "rules" => ["minlength" => 2, "required" => true], "value" => se($cat, "description", "", false)]); ?>
        <?php render_button(["text" => "Save", "type" => "submit"]); ?>
    </form>
</div>
<script>
    function fetch_images(event) {
        event.preventDefault();
        let breed_ele = document.getElementById("breed");
        if (breed_ele) {
            const val = breed_ele.value;
            if (!val) {
                flash("You need to select a breed first", "warning");
            } else {
                fetch(`/Project/api/get_images.php?breed_id=${val}`).then(resp => resp.json())
                    .then(data => {
                        let ic = document.getElementById("image_container");
                        if (ic) {
                            ic.innerHTML = ""; //reset
                            for (const d of data) {
                                let div = document.createElement("div");
                                div.className = "col";
                                let img = document.createElement("img");
                                img.src = d.url;
                                img.id = `img_${d.id}`;
                                img.style.width = "128px";
                                img.style.height = "128px";
                                img.style.aspectRatio = "scale-down";
                                img.onclick = () => {
                                    toggle_image(img);
                                };
                                div.appendChild(img);
                                let input = document.createElement("input");
                                input.type = "checkbox";
                                input.name = "images[]";
                                input.style.display = "none";
                                input.id = `url_${d.id}`;
                                input.value = d.id;
                                div.appendChild(input);
                                ic.appendChild(div);
                            }
                        }
                    });
            }
        }
    }

    function toggle_image(img) {
        img.classList.toggle("selected");
        const selected = img.classList.contains("selected");
        const id = img.id.split("_")[1];
        let input = document.getElementById(`url_${id}`);
        if (input) {
            input.checked = selected;
        }
    }
</script>
<style>
    .selected {
        border: 3px solid black;
    }
</style>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/footer.php");
?>