<?php if (isset($data)) : ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <?php se($data, "title"); ?>
            </h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">
                <?php se($data, "publishedDateTime"); ?>
            </h6>
            <div class="card-text" style="height: calc(5em); overflow-y: hidden; text-overflow: ellipsis;">
                <p style=" "><?php se($data, "excerpt"); ?></p>
            </div>
            <div class="card-text">
                <?php if (isset($data["featuredContent"])): ?>
                    <?php /* THIS IS DANGEROUS, don't blindly trust content from external/user sources */
                    // Added after the warning above, this will break any script tags passed.
                    // The idea is to help prevent XSS attacks, this is just a very basic example
                    echo str_ireplace("script>", "", $data["featuredContent"]);
                    ?>
                <?php endif; ?>
            </div>
            <?php $hasUrls = isset($data["srcUrl"]) || isset($data["webUrl"]) || isset($data["originalUrl"]); ?>
            <?php if ($hasUrls): ?>
                <div class="card-footer">
                    <ul class="list-group list-group-flush">
                        <?php if (isset($data["srcUrl"])): ?>
                            <li class="list-group-item">
                                <a href="<?php se($data["srcUrl"]); ?>"><?php se($data["srcUrl"]); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($data["webUrl"])): ?>
                            <li class="list-group-item">
                                <a href="<?php se($data["webUrl"]); ?>"><?php se($data["webUrl"]); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($data["originalUrl"])): ?>
                            <li class="list-group-item">
                                <a href="<?php se($data["originalUrl"]); ?>"><?php se($data["originalUrl"]); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (is_logged_in() || isset($data["view_url"])): ?>
                <div class="card-footer text-center">
                    <?php if (is_logged_in() && isset($data["is_watched"])): ?>
                        <?php /* is_watched toggle */
                        $redirect_url = se($_SERVER, "PHP_SELF", "", false) . '?' . http_build_query($_GET);
                        ?>
                        <form method="POST" action="<?php echo get_url("api/toggle_watched.php"); ?>">
                            <input type="hidden" name="guideId" value="<?php se($data, "id"); ?>" />
                            <input type="hidden" name="toggleWatched" />
                            <input type="hidden" name="route" value="<?php echo $redirect_url ?>" />
                            <button style="background-color: transparent; border: none !important;">
                                <?php render_like(["value" => $data["is_watched"]]); ?>
                            </button>
                        </form>

                    <?php endif; ?>
                    <?php if (has_role("Admin") && isset($data["delete_url"])): ?>
                        <?php /* admin only delete */ ?>
                        <a class="btn btn-danger" href="<?php se($data, "delete_url"); ?>">Delete</a>
                    <?php endif; ?>
                    <?php if (isset($data["view_url"])): ?>
                        <?php /* view url */ ?>
                        <a class="btn btn-primary" href="<?php se($data, "view_url"); ?>">View</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php endif;
