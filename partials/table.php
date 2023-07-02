<?php if (isset($data)) : ?>
    <?php
    //setup some variables for readability
    $_extra_classes = se($data, "extra_classes", "", false);
    $_title = se($data, "title", "", false);
    $_data = isset($data["data"]) ? $data["data"] : [];
    if (!$_data) {
        $_data = [];
    }
    $_view_url = se($data, "view_url", "", false);
    $_view_label = se($data, "view_label", "View", false);
    $_view_classes = se($data, "view_classes", "btn btn-primary", false);
    $_edit_url = se($data, "edit_url", "", false);
    $_edit_label = se($data, "edit_label", "Edit", false);
    $_edit_classes = se($data, "edit_classes", "btn btn-secondary", false);
    $_delete_url = se($data, "delete_url", "", false);
    $_delete_label = se($data, "delete_label", "Delete", false);
    $_delete_classes = se($data, "delete_classes", "btn btn-danger", false);
    $_primary_key_column = se($data, "primary_key", "id", false); // used for the url generation
    //TODO persist query params (future lesson)
    //
    // edge case that should consider a redesign
    $_post_self_form = isset($data["post_self_form"]) ? $data["post_self_form"] : [];
    // end edge case
    $_has_atleast_one_url = $_view_url || $_edit_url || $_delete_url || $_post_self_form;
    $_empty_message = se($data, "empty_message", "No records to show", false);
    $_header_override = isset($data["header_override"]) ? $data["header_override"] : []; // note: this is as csv string or an array
    // assumes csv list; explodes to array
    if (is_string($_header_override)) {
        $_header_override = explode(",", $_header_override);
    }
    $_ignored_columns = isset($data["ignored_columns"]) ? $data["ignored_columns"] : []; // note: this is as csv string or an array
    // assumes csv list; explodes to array
    if (is_string($_ignored_columns)) {
        $_ignored_columns = explode(",", $_ignored_columns);
    }
    // attempt to get headers from $_data if no override
    if (!$_header_override && count($_data) > 0) {
        $_header_override = array_filter(array_keys($_data[0]), function ($v) use ($_ignored_columns) {
            return !in_array($v, $_ignored_columns);
        });
    }

    ?>
    <?php if ($_title) : ?>
        <h3><?php se($title); ?></h3>
    <?php endif; ?>
    <table class="table <?php se($_extra_classes); ?>">
        <?php if ($_header_override) : ?>
            <thead>
                <?php foreach ($_header_override as $h) : ?>
                    <th><?php se($h); ?></th>
                <?php endforeach; ?>
                <?php if ($_has_atleast_one_url) : ?>
                    <th>Actions</th>
                <?php endif; ?>
            </thead>
        <?php endif; ?>
        <tbody>
            <?php if (is_array($_data) && count($_data) > 0) : ?>
                <?php foreach ($_data as $row) : ?>
                    <tr>
                        <?php foreach (array_values($row) as $v) : ?>
                            <?php if (!in_array($v, $_ignored_columns)) : ?>
                                <td><?php se($v); ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($_has_atleast_one_url) : ?>
                            <td>
                                <?php if ($_view_url) : ?>
                                    <a href="<?php se($_view_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_view_classes); ?>"><?php se($_view_label); ?></a>
                                <?php endif; ?>
                                <?php if ($_edit_url) : ?>
                                    <a href="<?php se($_edit_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_edit_classes); ?>"><?php se($_edit_label); ?></a>
                                <?php endif; ?>
                                <?php if ($_delete_url) : ?>
                                    <a href="<?php se($_delete_url); ?>?<?php se($_primary_key_column); ?>=<?php se($row, $_primary_key_column); ?>" class="<?php se($_delete_classes); ?>"><?php se($_delete_label); ?></a>
                                <?php endif; ?>
                                <?php if ($_post_self_form) : ?>
                                    <!-- TODO refactor -->
                                    <form method="POST">
                                        <input type="hidden" name="<?php se($_post_self_form, "name", $_primary_key_column); ?>" value="<?php se($row, $_primary_key_column); ?>" />
                                        <input type="submit" class="<?php se($_post_self_form, "classes"); ?>" value="<?php se($_post_self_form, "label", "Submit"); ?>" />
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="100%"><?php se($_empty_message); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>