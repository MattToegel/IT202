<?php
require_once(__DIR__."/../../../lib/functions.php");
$breed_id = se($_GET, "breed_id", -1, false);
$result = get_images_by_breed_id($breed_id, true);
echo json_encode($result);
?>