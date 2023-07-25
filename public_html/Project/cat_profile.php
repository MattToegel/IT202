<?php /* Note this file is different than admin/cat_profile.php*/ ?>
<?php
require(__DIR__ . "/../../partials/nav.php");
$id = se($_GET, "id", -1, false);
if ($id <= 0) {
    flash("Invalid cat", "danger");
    $url = "browse.php?" . http_build_query($_GET);
    error_log("redirecting to " . var_export($url, true));
    redirect(get_url($url));
}
if (count($_POST) > 0) {
    $action = se($_POST, "action", "", false);
    $requestor_notes = se($_POST, "details", "", false);
    if ($action == "adopt") {
        $intent_id = create_intent($id, get_user_id(), null, $action, $requestor_notes);
        if ($intent_id > 0) {
            flash("Your request has been submitted!", "success");
        } else {
            flash("There was a problem submitting your request", "danger");
        }
    } else if ($action == "foster") {
        $intent_id = create_intent($id, get_user_id(), null, $action, $requestor_notes);
        if ($intent_id > 0) {
            flash("Your request has been submitted!", "success");
        } else {
            flash("There was a problem submitting your request", "danger");
        }
    }
}
$_GET["image_limit"] = 10;
$cat = search_cats();
$cat = $cat[0];
$breed_id = se($cat, "breed_id", 0, false);
$breed = [];
if ($breed_id != 0) {
    $breed = get_breed_by_id($breed_id);
    error_log("breed: " . var_export($breed, true));
}
?>
<div class="container-fluid">

    <h1>Hello!</h1>
    <div class="card">
        <div class="card-header text-center">
            <?php se($cat, "status"); ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title"><?php se($cat, "name"); ?> - <?php se($cat, "age"); ?> year old - <?php se($cat, "sex"); ?> - <?php se($cat, "breed"); ?></h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="row">
                        <?php /* handle image*/
                        $urls = isset($cat["urls"]) ? $cat["urls"] : "";
                        error_log("urls data: " . var_export($urls, true));
                        $urls = explode(",", $urls);
                        error_log("urls data after explode:" . var_export($urls, true));
                        ?>
                        <?php foreach ($urls as $url) : ?>
                            <div class="col">
                                <img class="p-3" style="width: 100%; aspect-ratio: 1; object-fit: scale-down; max-height: 256px;" src="<?php se($url, null, get_url("images/black_cat_vector_svg.jpg")); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col">
                    <div><strong>About Me:</strong><br>
                        <?php se($cat, "description"); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>Breed Info</h5>
                    <div><strong>Breed: </strong><?php se($cat, "breed"); ?></div>
                    <div><strong>Alt. Name(s): </strong><?php se($breed, "alt_names", "-"); ?></div>
                    <div><strong>Origin: </strong><?php se($breed, "origin", "-"); ?></div>
                    <div><strong>Description: </strong><?php se($breed, "description", "-"); ?></div>
                    <div><strong>Temperament: </strong><?php se($cat, "temperament", "-"); ?></div>
                </div>
                <div class="col">
                    <div><strong>Indoor: </strong><?php render_like(["value" => se($breed, "indoor", 0, false)]); ?></div>
                    <div><strong>Lap: </strong><?php render_like(["value" => se($breed, "lap", 0, false)]); ?></div>
                    <div><strong>Hairless: </strong><?php render_like(["value" => se($breed, "hairless", 0, false)]); ?></div>
                    <div><strong>Natural: </strong><?php render_like(["value" => se($breed, "natural", 0, false)]); ?></div>
                    <div><strong>Rare: </strong><?php render_like(["value" => se($breed, "rare", 0, false)]); ?></div>
                    <div><strong>Rex: </strong><?php render_like(["value" => se($breed, "rex", 0, false)]); ?></div>
                    <div><strong>Suppressed Tail: </strong><?php render_like(["value" => se($breed, "suppressed_tail", 0, false)]); ?></div>
                    <div><strong>Short Legs: </strong><?php render_like(["value" => se($breed, "short_legs", 0, false)]); ?></div>
                    <div><strong>Hypoallergenic: </strong><?php render_like(["value" => se($breed, "hypoallergenic", 0, false)]); ?></div>
                </div>
                <div class="col">
                    <div><strong>Adaptability: </strong><?php render_stars(["value" => se($breed, "adaptability", -1, false)]); ?></div>
                    <div><strong>Affection Level: </strong><?php render_stars(["value" => se($breed, "affection_level", -1, false)]); ?></div>
                    <div><strong>Child Friendly: </strong><?php render_stars(["value" => se($breed, "child_friendly", -1, false)]); ?></div>
                    <div><strong>Cat Friendly: </strong><?php render_stars(["value" => se($breed, "cat_friendly", -1, false)]); ?></div>
                    <div><strong>Dog Friendly: </strong><?php render_stars(["value" => se($breed, "dog_friendly", -1, false)]); ?></div>
                    <div><strong>Stranger Friendly: </strong><?php render_stars(["value" => se($breed, "stranger_friendly", -1, false)]); ?></div>
                    <div><strong>Grooming: </strong><?php render_stars(["value" => se($breed, "grooming", -1, false)]); ?></div>
                    <div><strong>Health Issues: </strong><?php render_stars(["value" => se($breed, "health_issues", -1, false)]); ?></div>
                    <div><strong>Intelligence: </strong><?php render_stars(["value" => se($breed, "intelligence", -1, false)]); ?></div>
                    <div><strong>Shedding Level: </strong><?php render_stars(["value" => se($breed, "shedding_level", -1, false)]); ?></div>
                    <div><strong>Social Needs: </strong><?php render_stars(["value" => se($breed, "social_needs", -1, false)]); ?></div>
                    <div><strong>Energy Level: </strong><?php render_stars(["value" => se($breed, "energy_level", -1, false)]); ?></div>
                    <div><strong>vocalisation: </strong><?php render_stars(["value" => se($breed, "vocalisation", -1, false)]); ?></div>
                    <div><strong>Obedience: </strong><?php render_stars(["value" => se($breed, "bidability", -1, false)]); ?></div>
                    <div><strong>Experimental: </strong><?php render_stars(["value" => se($breed, "experimental", -1, false)]); ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>More Info</h5>
                    <?php
                    $urls = se($breed, "urls", "", false);
                    $urls = explode(",", $urls); ?>
                    <ul>
                        <?php foreach ($urls as $url) : ?>
                            <li><a href="<?php se($url); ?>"><?php se($url); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#actionModal" data-bs-action="adopt">Adopt</button>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#actionModal" data-bs-action="foster">Foster</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="actionModalLabel">Action Prompt</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <?php
                            render_input(["type" => "textarea", "name" => "details", "label" => "Request Details", "rules" => ["required" => true]]);
                            ?>
                            <?php render_input(["type" => "hidden", "id" => "action", "name" => "action", "value" => ""]); ?>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <?php
                        render_button(["type" => "button", "color" => "secondary", "extras" => ["data-bs-dismiss" => "modal"], "text" => "Close"]);
                        render_button(["type" => "submit", "color" => "primary", "text" => "Submit Request"]);
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const actionModal = document.getElementById('actionModal')
        if (actionModal) {
            actionModal.addEventListener('show.bs.modal', event => {
                // Button that triggered the modal
                const button = event.relatedTarget;
                // Extract info from data-bs-* attributes
                const action = button.getAttribute('data-bs-action');
                // If necessary, you could initiate an Ajax request here
                // and then do the updating in a callback.
                const catName = "<?php se($cat, "name"); ?>";

                // Update the modal's content.
                const modalTitle = actionModal.querySelector('.modal-title')

                modalTitle.textContent = `${action} ${catName}`
                const actionInput = actionModal.querySelector("#action");
                actionInput.value = action;
            })
        }
    </script>
    <style>
        .modal-title {
            text-transform: capitalize;
        }
    </style>
    <?php
    require_once(__DIR__ . "/../../partials/footer.php");
    ?>