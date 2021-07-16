<?php
//This file requires any $results associative array and functions.php
//note: normally we could also show "no results" here if the $results are length 0
//but we can't nicely show the table headers without a success query.
//You could refactor this to receive separate variables, one for header and one for data
//if you wish to make it more reusable
if (!isset($results)) {
    $results = [];
} ?>
<table class="table">
    <?php foreach ($results as $index => $record) : ?>
        <?php if ($index == 0) : ?>
            <thead>
                <?php foreach ($record as $column => $value) : ?>
                    <th><?php se($column); ?></th>
                <?php endforeach; ?>
            </thead>
        <?php endif; ?>
        <tr>
            <?php foreach ($record as $column => $value) : ?>
                <td><?php se($value, null, "N/A"); ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>