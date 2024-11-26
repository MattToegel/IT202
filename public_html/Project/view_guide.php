<?php
require_once(__DIR__ . "/../../partials/nav.php");

$id = (int)se($_GET, "id", -1, false);
$guide = [];
if ($id > 0) {
    $sql = "SELECT SCG.id,title,excerpt,srcUrl, webUrl, originalUrl, featuredContent, publishedDateTime, GROUP_CONCAT(DISTINCT SCT.name) AS topics, publishedDateTime, GROUP_CONCAT(DISTINCT SCP.name) AS providers, type FROM SC_Guides as SCG
JOIN SC_GuideImages as SCGI on SCGI.guide_id = SCG.id
JOIN SC_Images SCI on SCGI.image_id = SCI.id
JOIN SC_GuideProviders as SCGP on SCGP.guide_id = SCG.id
JOIN SC_Providers as SCP on SCGP.provider_id = SCP.id
JOIN SC_GuideTopics as SCGT on SCGT.guide_id = SCG.id
JOIN SC_Topics as SCT on SCGT.topic_id = SCT.id WHERE SCG.id = :id GROUP BY SCG.id";
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $guide = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching guide: " . var_export($e, true));
        flash("Error fetching guide", "danger");
    }
} else {
    flash("Invalid guide", "danger");
}
?>

<?php if($guide):?>
    <div style="width: 80%;" class="mx-auto">
        <?php guide_card($guide);?>
    </div>
<?php endif;?>
<?php 
require_once(__DIR__ . "/../../partials/flash.php");