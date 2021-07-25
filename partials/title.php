<?php /* this file requires $title to be set prior */
if (!isset($title)) {
    $title = "";
} ?>
<h3><span class="badge bg-secondary" style="text-transform: capitalize;"><?php se($title); ?></span></h3>
<script>
    window.addEventListener("load", () => {
        document.getElementsByTagName("title")[0].innerText = "<?php se($title); ?>";
    }, false);
</script>