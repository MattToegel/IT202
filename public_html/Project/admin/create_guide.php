<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

//TODO handle stock fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $page =  (int)se($_POST, "page", 1, false);
    $guides = [];
    if ($action === "fetch") { // api logic
        $result = fetch_guides($page);
        error_log("Data from API" . var_export($result, true));
        if ($result) {
            $guides = array_map(function ($item) {
                $item["is_api"] = 1;
                return $item;
            }, $result);
        }
    } else if ($action === "create") { // custom logic
        $desired_columns = ["path", "title", "excerpt", "srcUrl", "webUrl", "originalUrl", "featuredContent", "publishedDateTime", "type"];
        $guide = $_POST;
        foreach ($guide as $k => $v) {
            if (!in_array($k, $desired_columns)) {
                unset($guide[$k]);
            }
        }
        $guides = [$guide];
        error_log("Cleaned up POST: " . var_export($guides, true));
    }

    //insert data
    try {
        if ($action == "create") {
            if (isset($_POST["images"])) {
                $images = $_POST["images"];
                $mapped = array_map(function ($img) {
                    return ["url" => $img, "width" => 512, "height" => 512];
                }, $images);
                error_log("Inserting images: " . var_export($mapped, true));
                insert("SC_Images", $mapped, ["update_duplicate" => true]);
            }
        }
        transform_and_insert($guides);
        // mappings not fully handled in transform_and_insert()
        if ($action == "create") {
            if (isset($_POST["topic"])) {
                try {
                    $db = getDB();
                    $stmt = $db->prepare("INSERT INTO SC_GuideTopics (topic_id, guide_id)
                VALUES (:topic_id, 
                (SELECT id FROM SC_Guides where title = :title)
                )");
                    $stmt->execute([":topic_id" => $_POST["topic"], ":title" => $_POST["title"]]);
                } catch (Exception $e) {
                    error_log("Error mapping new guide to topic: " . var_export($e, true));
                }
            }
            if (isset($_POST["provider"])) {
                try {
                    $db = getDB();
                    $stmt = $db->prepare("INSERT INTO SC_GuideProviders (provider_id, guide_id)
                VALUES (:provider_id, 
                (SELECT id FROM SC_Guides where title = :title)
                )");
                    $stmt->execute([":provider_id" => $_POST["provider"], ":title" => $_POST["title"]]);
                } catch (Exception $e) {
                    error_log("Error mapping new guide to provider: " . var_export($e, true));
                }
            }
        }

        flash("Saved changes", "success");
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An unhandled duplicate entry occurred", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
}


// dynamically generate form data

// generate from columns
$db = getDB();
try {
    $stmt = $db->prepare("SHOW COLUMNS FROM `SC_Guides`");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    /*echo "<pre>";
    var_dump($columns);
    echo "</pre>";*/
} catch (PDOException $e) {
    error_log("Error getting table info" . var_export($e, true));
}
$editable_fields = ["path", "title", "excerpt", "srcUrl", "webUrl", "originalUrl", "featuredContent", "publishedDateTime", "type"];
$form = [];
foreach ($columns as $col) {
    $name = $col["Field"];
    $col_type = strtolower($col["Type"]);
    if (in_array($name, $editable_fields)) {
        $type = "text";
        if (str_contains($col_type, "int") || in_array($col_type, ["float", "decimal", "double"])) {
            $type = "number";
        } else if (str_contains($col_type, "date") || str_contains($col_type, "time")) {
            $type = "datetime";
        }
        if (str_contains($name, "url")) {
            $type = "url";
        }
        array_push($form, [
            "name" => $col["Field"],
            "type" => $type,
            "label" => $name,
            "rules" => ["required" => true]
        ]);
    }
}
// generate select options
$topics = get_topics();
$providers = get_providers();
$types = get_types();

error_log("form: " . var_export($form, true));
?>
<div class="container-fluid">
    <h3>Create or Fetch Guide</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "number", "name" => "page", "placeholder" => "Page", "value" => 1, "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <?php foreach ($form as $field): ?>
                <?php render_input($field); ?>
            <?php endforeach; ?>
            <?php render_input(["name" => "topic", "label" => "Topic",  "type" => "select", "options" => $topics]); ?>
            <?php render_input(["name" => "type", "label" => "Type",  "type" => "select", "options" => $types]); ?>
            <?php render_input(["name" => "provider", "label" => "Providers", "type" => "select", "options" => $providers]); ?>
            <?php render_input(["type" => "url", "name" => "images[]", "label" => "Images"]) ?>
            <?php render_button(["id" => "imageButton", "text" => "Add Image", "type" => "button", "onclick" => "addImage(event)"]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }

    function addImage(event) {
        event.preventDefault(); //prevent form submission
        let imgInput = document.getElementsByName("images[]");
        imgInput = imgInput[imgInput.length - 1];
        let clone = imgInput.cloneNode();
        clone.id = new Date().getTime();
        let div = document.createElement("div");
        div.className = "mb-3";
        div.append(clone);
        event.srcElement.before(div);
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>