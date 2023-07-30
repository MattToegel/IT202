<?php if (isset($data)) : error_log("cat data: " . var_export($data, true)); ?>
    <div class="card" style="width:15em">
        <div class="card-header">
            <?php se($data, "status", "N/A"); ?>
            <?php if (se($data, "username", "", false)) : ?> by
                <a href="<?php get_url("profile.php?id=", true);
                            se($data, "owner_id"); ?>"><?php se($data, "username"); ?></a> - <?php se($data, "last_updated"); ?>
            <?php endif; ?>
        </div>
        <?php /* handle image*/
        $urls = isset($data["urls"]) ? $data["urls"] : "";
        error_log("urls data: " . var_export($urls, true));
        $urls = explode(",", $urls);
        error_log("urls data after explode:" . var_export($urls, true));
        ?>
        <img class="p-3" style="width: 100%; aspect-ratio: 1; object-fit: scale-down; max-height: 256px;" src="<?php se($urls, 0, get_url("images/black_cat_vector_svg.jpg")); ?>" />
        <div class="card-body">
            <h5 class="card-title"><?php se($data, "name"); ?></h5>
            <h6 class="card-subtitle"><?php se($data, "breed", "Other/Mixed"); ?></h6>
            <h6 class="card-subtitle text-body-secondary"><?php se($data, "temperament"); ?></h6>
            <p class="card-text">
                Sex: <?php se($data, "sex"); ?>
                <?php if (se($data, "fixed", "0", false) == "1") : ?>
                    <i alt="Fixed" title="Fixed" class="bi bi-scissors"></i>
                <?php endif; ?>
                <br>
                <?php
                $age = (int)se($data, "age", 0, false);
                if ($age <= 0) {
                    $age = "<1";
                } ?>
                Age: <?php se($age); ?>
                <br>
            </p>
            <p class="card-text"><strong>About me:</strong><br><?php se($data, "description"); ?></p>
        </div>
        <div class="card-footer">
            <?php $id = se($data, "id", -1, false);
            $is_single_view = !isset($_GET["id"]);
            ?>
            <div class="row">
                <?php if ($is_single_view) : /* if used in single view don't add link*/ ?>
                    <a class="btn btn-primary col" href="<?php get_url("cat_profile.php?id=$id", true); ?>">Check Meowt!</a>
                <?php endif; ?>
            </div>
            <?php if (has_role("Admin")) : ?>
                <div class="row mt-1 g-1">
                    <a class="btn btn-secondary col me-1" href="<?php get_url("admin/cat_profile.php?id=$id", true); ?>">Edit</a>
                    <?php

                    $search_filters = array_filter($_GET, function ($value) {
                        return ($value !== null && $value !== '');
                    });
                    if (!isset($_GET["id"])) {
                        $search_filters["id"] = $id;
                    }
                    $query_string = http_build_query($search_filters);
                    $url = get_url("admin/disable_cat_profile.php?$query_string");
                    ?>
                    <a class="btn btn-danger col" href="<?php se($url); ?>">Remove</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>