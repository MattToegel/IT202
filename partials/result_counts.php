<?php
if (!isset($result_count)) {
    $message = "Dev Note: result_count not set";
    error_log($message);
    flash($message);
    $result_count = -1;
}
if (!isset($total_count)) {
    $message = "Dev Note: total_count not set";
    error_log($message);
    flash($message);
    $total_records = -1;
}
?>
<header>
    <div class="row">
        <div class="col-1">Results: <?php se("$result_count/$total_count"); ?></div>
    </div>
</header>