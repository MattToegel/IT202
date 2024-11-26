<?php

function fetch_guides($page = 1)
{
    $page = se($_GET, "page", "1", false);
    $data = [];
    $endpoint = "https://starcraft-ii.p.rapidapi.com/learning/page/$page/";
    $isRapidAPI = true;
    $rapidAPIHost = "starcraft-ii.p.rapidapi.com";
    $result = get($endpoint, "SC_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    //error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
        $result = isset($result["value"]) ? $result["value"] : [];
    } else {
        $result = [];
    }
    return $result;
}

function transform_and_insert($result)
{
    $topics = [];
    $providers = [];
    $images = [];
    $guides = [];
    $guideImages = [];
    $guideProviders = [];
    $guideTopics = [];
    $desired_columns = ["path", "title", "excerpt", "sourceUrl", "webUrl", "originalUrl", "featuredContent", "publishedDateTime", "type"];
    foreach ($result as $g) {
        //error_log("Guide: " . var_export($g, true));
        if (isset($g["images"])) {
            // build images data
            foreach ($g["images"] as $image) {
                if (isset($image["isCached"])) {
                    unset($image["isCached"]);
                }
                array_push($images, $image);
                // prepare binding map
                if (isset($g["title"]) && isset($image["url"])) {
                    array_push($guideImages, [":url" => $image["url"], ":guide" => $g["title"]]);
                }
            }
        }
        //build providers
        if (isset($g["provider"])) {
            array_push($providers, $g["provider"]);
            // prepare binding map
            array_push($guideProviders, [":domain" => $g["provider"]["domain"], ":guide" => $g["title"]]);
        }
        //build topics
        if (isset($g["topics"])) {
            foreach ($g["topics"] as $topic) {
                array_push($topics, ["name" => $topic]);
                // prepare binding map
                array_push($guideTopics, [":name" => $topic, ":guide" => $g["title"]]);
            }
        }
        // build guides
        $keys = array_keys($g);
        foreach ($keys as $key) {
            if (!in_array($key, $desired_columns)) {
                unset($g[$key]);
            }
        }
        if (isset($g["sourceUrl"])) {
            $g["srcUrl"] = $g["sourceUrl"];
            unset($g["sourceUrl"]);
        }
        // add missing fields
        foreach ($desired_columns as $key) {
            //patch for desired columns typo
            $k = $key === "sourceUrl" ? "srcUrl" : $key;
            if (!isset($g[$k])) {
                $g[$k] = null;
            }
        }
        array_push($guides, $g);
    }
    error_log("guides: " . var_export($guides, true));
    // start inserts
    if (!empty($images)) {
        insert("SC_Images", $images, ["update_duplicate" => true]);
    }
    if (!empty($providers)) {
        insert("SC_Providers", $providers, ["update_duplicate" => true]);
    }
    if (!empty($topics)) {
        insert("SC_Topics", $topics, ["update_duplicate" => true]);
    }
    insert("SC_Guides", $guides, ["update_duplicate" => true, "debug" => false]);

    // apply mappings
    if (!empty($guideImages)) {
        $qimage = "INSERT INTO SC_GuideImages(guide_id, image_id)
        VALUES (
        (SELECT id from SC_Guides where title = :guide LIMIT 1),
        (SELECT id from SC_Images where url = :url LIMIT 1)
        ) ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP";
        $db = getDB();
        foreach ($guideImages as $pair) {

            try {
                $stmt = $db->prepare($qimage);
                $stmt->execute($pair);
            } catch (PDOException $e) {
                error_log("Image insert failed: " . var_export($e->errorInfo, true));
            }
        }
    }
    if (!empty($guideTopics)) {
        $qtopic = "INSERT INTO SC_GuideTopics(guide_id, topic_id)
        VALUES (
        (SELECT id from SC_Guides where title = :guide LIMIT 1),
        (SELECT id from SC_Topics where name = :name LIMIT 1)
        ) ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP";
        foreach ($guideTopics as $pair) {

            try {
                $stmt = $db->prepare($qtopic);
                $stmt->execute($pair);
            } catch (PDOException $e) {
                error_log("Topic insert failed: " . var_export($e->errorInfo, true));
            }
        }
    }
    if (!empty($guideProviders)) {
        $qprovider = "INSERT INTO SC_GuideProviders(guide_id,provider_id)
        VALUES (
        (SELECT id from SC_Guides where title = :guide LIMIT 1),
        (SELECT id from SC_Providers where domain = :domain LIMIT 1)
        ) ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP";
        foreach ($guideProviders as $pair) {

            try {
                $stmt = $db->prepare($qprovider);
                $stmt->execute($pair);
            } catch (PDOException $e) {
                error_log("Provider insert failed: " . var_export($e->errorInfo, true));
            }
        }
    }
}

function get_topics()
{
    $topics = [];
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT DISTINCT id,name FROM SC_Topics");
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $topics =  array_map(fn($t) => [$t["id"] => $t["name"]], $r);
            array_unshift($topics, ["-1" => "Select"]);
        }
    } catch (PDOException $e) {
        error_log("Error fetching topics: " . var_export($e, true));
    }
    return $topics;
}

function get_providers()
{
    $providers = [];
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT DISTINCT id,name FROM SC_Providers");
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $providers = array_map(fn($t) => [$t["id"] => $t["name"]], $r);
            array_unshift($providers, ["-1" => "Select"]);
        }
    } catch (PDOException $e) {
        error_log("Error fetching providers: " . var_export($e, true));
    }
    return $providers;
}

function get_types()
{
    $types = [];
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT DISTINCT type FROM SC_Guides");
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $types = array_map(fn($t) => [$t["type"] => $t["type"]], $r);
            array_unshift($types, ["-1" => "Select"]);
        }
    } catch (PDOException $e) {
        error_log("Error fetching types: " . var_export($e, true));
    }
    return $types;
}

function guide_card($data = array()){
    include(__DIR__ . "/../partials/guide_card.php");
}