<?php
require("nav.php");
$db = getDB();
//generally try to avoid SELECT *, but this is about being dynamic so I'm using it this time
$query = "SELECT * FROM Samples LIMIT 500"; //TODO change table name and desired columns
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<pre>" . var_export($e, true) . "</pre>";
}
?>
<h3>View Samples</h3>
<?php if (count($results) == 0) : ?>
    <p>No results to show</p>
<?php else : ?>
    <table>
        <?php foreach ($results as $index => $record) : ?>
            <?php if ($index == 0) : ?>
                <thead>
                    <?php foreach ($record as $column => $value) : ?>
                        <th><?php se($column); ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </thead>
            <?php endif; ?>
            <tr>
                <?php foreach ($record as $column => $value) : ?>
                    <td><?php se($value, null, "N/A"); ?></td>
                <?php endforeach; ?>


                <td>
                    <a href="dynamic_edit.php?id=<?php se($record, "id"); ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>