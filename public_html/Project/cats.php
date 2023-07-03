<?php
require(__DIR__ . "/../../partials/nav.php");
$result = get("https://api.thecatapi.com/v1/images/search", "CAT_API_KEY", ["limit" => 10, "page" => 0, "has_breeds" => "true", "include_breeds" => "true", "include_categories" => "true"], false);
error_log("Response: " . var_export($result, true));
if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
    $result = json_decode($result["response"], true);
} else {
    $result = [];
}
?>
<div class="container-fluid">
    <h1>Random Cat Images - Demo</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <div class="row ">
        <?php foreach ($result as $cat) : ?>
            <div class="col">
                <div class="card" style="width: 15em; height:350px">
                    <img src="<?php se($cat["url"]); ?>" style="width: 100%; max-height:256px; object-fit:scale-down" />
                    <div class="card-body">
                        <h5 class="card-title">Cat</h5>
                        <p class="card-text">
                            <?php
                            $has_breeds = isset($cat["breeds"]) && count($cat["breeds"]) > 0;
                            //output prep
                            if ($has_breeds) {
                                $breed_str = trim(join(", ", array_map(function ($breed) {
                                    return $breed["name"];
                                }, $cat["breeds"])));
                            }
                            ?>
                            <?php if ($has_breeds) : ?>
                                Breed<?php echo count($cat["breeds"]) > 1 ? "s" : ""; ?> in photo: <?php se($breed_str); ?>
                            <?php endif; ?>
                        </p>
                        <p class="card-text">
                            <?php
                            $has_categories = isset($cat["categories"]) && count($cat["categories"]) > 0;
                            if ($has_categories) {
                                $category_str = trim(join(", ", array_map(function ($c) {
                                    return $c["name"];
                                }, $cat["categories"])));
                            }
                            ?>
                            <?php if ($has_categories) : ?>
                                <em>Cat</em>egories: <?php se($category_str); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>