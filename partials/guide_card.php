<?php if (isset($data)) : ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <?php se($data, "title"); ?>
            </h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">
                <?php se($data, "publishedDateTime");?>
            </h6>
            <div class="card-text" style="max-height: 5em; overflow-y: auto">
                <?php se($data, "excerpt"); ?>
            </div>
            <div class="card-text">
                <?php if(isset($data["featuredContent"])): ?>
                    <?php /* THIS IS DANGEROUS, don't blindly trust content from external/user sources */
                    echo $data["featuredContent"];
                    ?>
                <?php endif;?>
            </div>
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
        </div>
    </div>
<?php endif;
