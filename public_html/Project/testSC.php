<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["page"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $page = se($_GET, "page", "1", false);
    $data = [];
    $endpoint = "https://starcraft-ii.p.rapidapi.com/learning/page/$page/";
    $isRapidAPI = true;
    $rapidAPIHost = "starcraft-ii.p.rapidapi.com";
    $result = get($endpoint, "SC_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas

    //error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
        $result = $result["value"];

        $topics = [];
        $providers = [];
        $images = [];
        $guides = [];
        $gimages = [];
        $gproviders = [];
        $gtopics = [];
        $desired_columns = ["path", "title", "excerpt", "sourceUrl", "webUrl", "originalUrl", "featuredContent", "publishedDateTime", "type"];
        foreach ($result as $g) {
            //error_log("Guide: " . var_export($g, true));
            if (isset($g["images"])) {
                // build images data
                foreach ($g["images"] as $image) {
                    unset($image["isCached"]);
                    array_push($images, $image);
                    array_push($gimages, ["url" => $image["url"], "guide" => $g["title"]]);
                }
                //build providers
                if (isset($g["provider"])) {
                    array_push($providers, $g["provider"]);
                    array_push($gproviders, ["domain" => $g["provider"]["domain"], "guide" => $g["title"]]);
                }
                //build topics
                foreach ($g["topics"] as $topic) {
                    array_push($topics, ["name" => $topic]);
                    array_push($gimages, ["name" => $topic, "guide" => $g["title"]]);
                }
                // build guides
                $keys = array_keys($g);
                foreach ($keys as $key) {
                    if (!in_array($key, $desired_columns)) {
                        unset($g[$key]);
                    }
                }

                $g["srcUrl"] = $g["sourceUrl"];
                unset($g["sourceUrl"]);

                // add missing fields
                foreach (array_keys($g) as $key) {
                    if (!in_array($key, $desired_columns)) {
                        $g[$key] = "";
                    }
                }
                array_push($guides, $g);
            }
        }
        // $guides = [$guides[0]];
        error_log("guides: " . var_export($guides, true));
        // start inserts
        insert("SC_Images", $images, ["update_duplicate" => true]);
        insert("SC_Providers", $providers, ["update_duplicate" => true]);
        insert("SC_Topics", $topics, ["update_duplicate" => true]);
        foreach ($guides as $g) {
            try {
                insert("SC_Guides", $g);
            } catch (Exception $e) {
                //ignore it, data is botched
            }
        }
        // apply mappings
        $qimage = "INSERT INTO SC_GuideImages(image_id, guide_id)
        VALUES (
        (SELECT id from SC_Guides where title = :guide LIMIT 1),
        (SELECT id from SC_Images where url = :url LIMIT 1)
        )";
        foreach ($gimages as $pair) {
            $db = getDB();
            try {
                $stmt = $db->prepare($qimage);
                $stmt->execute($pair);
            } catch (Exception $e) {
                error_log("insert failed");
            }
        }
        $qtopic = "INSERT INTO SC_GuideTopics(topic_id, guide_id)
        VALUES (
        (SELECT id from SC_Guides where title = :guide LIMIT 1),
        (SELECT id from SC_Topics where name = :name LIMIT 1)
        )";
        foreach ($gtopics as $pair) {
            $db = getDB();
            try {
                $stmt = $db->prepare($qtopic);
                $stmt->execute($pair);
            } catch (Exception $e) {
                error_log("insert failed");
            }
        }

        //insert("SC_Guides", $guides,  ["update_duplicate" => true, "debug" => true]);
    } else {
        $result = [];
    }
}
?>
<div class="container-fluid">
    <h1>Stock Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Page</label>
            <input name="page" />
            <input type="submit" value="Fetch Guides" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $guide) : ?>
                <pre>
            <?php var_export($guide);
            ?>
            </pre>
                <table style="display: none">
                    <thead>
                        <?php foreach ($guide as $k => $v) : ?>
                            <td><?php se($k); ?></td>
                        <?php endforeach; ?>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($guide as $k => $v) : ?>
                                <td><?php se($v); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
