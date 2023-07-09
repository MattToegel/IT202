<?php

function get_images_from_api_by_breed($api_breed_id)
{
    $data = [
        "has_breeds" => "true", "order" => "RANDOM", "include_breeds" => "true",
        "limit" => 25
    ];
    if (isset($api_breed_id) && !empty($api_breed_id)) {
        $data["breed_ids"] = $api_breed_id;
    }
    $results = get("https://api.thecatapi.com/v1/images/search", "CAT_API_KEY",  $data, false);
    if(isset($results) && isset($results["status"]) && $results["status"] == 200){
        return json_decode($results["response"], true);
    }
    return [];
}

function _store_images($images){
    $data = [];
    error_log("incoming images: " . var_export($images,true));
    foreach($images as $img){
        if(isset($img["breeds"])){
            foreach($img["breeds"] as $breed){
                array_push($data, ["api_id"=>$img["id"], "url"=>$img["url"], "api_breed_id"=> $breed["id"]]);
            }
        }
    }
    $query = "INSERT INTO CA_Images(breed_id,api_breed_id, api_id, url) VALUES ";
    $values = [];
    $placeholders = [];
    $i = 0;
    foreach($data as $record){
        $placeholders[] = "((SELECT id from CA_Breeds WHERE api_id = :b$i),:api_breed_id$i, :api_id$i, :url$i)";
        $values[] = [":b$i"=>$record["api_breed_id"], ":api_breed_id$i"=>$record["api_breed_id"], ":api_id$i"=>$record["api_id"], ":url$i"=>$record["url"]];
        $i++;
        
    }
    $query .= implode(',', $placeholders);
    $query .= " ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP()";
    error_log("store images query: " . var_export($query, true));
    $db = getDB();
    $stmt = $db->prepare($query);
    foreach($values as $index=>$val){
        foreach($val as $key=>$v){
            $stmt->bindValue($key, $v);
        }
    }
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error inserting image data: " . var_export($e, true));
    }
}

function get_breeds()
{
    $db = getDB();
    $query = "SELECT id, api_id, name FROM CA_Breeds";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute();
        $result = $stmt->fetchAll();
        //error_log("Breed results: " . var_export($result, true));
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching breeds from db: " . var_export($e, true));
    }
    return [];
}

function get_images_by_breed_id($breed_id, $random = false, $retries=3)
{
    if($retries <= 0){
        return [];
    }
    $db = getDB();

    $query = "SELECT id, url, max(modified) as 'last_modified' FROM CA_Images ";
    $params = [];
    if($breed_id > 0){
        $query .= " WHERE breed_id = :id";
        $params[":id"] = $breed_id;
    }
    $query .= " GROUP BY id, url";
    if($random){
        $query .= " ORDER BY RAND()";
    }
    $query .= " LIMIT 10";
    error_log("get images by breed query: " . var_export($query, true));
    $stmt = $db->prepare($query);
    $images = [];
    try {
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        if ($result) {
            $images = $result;
        }
    } catch (PDOException $e) {
        error_log("Error fetching images by internal breed id: " . var_export($e, true));
    }
    
    //check cache expirey
    $fetchFromAPI = false;
    $cache_life_in_hours = 6;
    if (count($images) > 0) {
        $date = strtotime($images[0]["last_modified"]);
        // Convert MySQL timestamp to PHP DateTime object
        error_log("mysql date $date");
        if(is_numeric($date)){
            $date1 = DateTime::createFromFormat("U", $date);
        }
        else{
            $date1 = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        }
        

        // Current date
        $now = new DateTime();

        // Calculate difference
        $interval = $date1->diff($now);

        // Get number of hours
        // The 'h' property of the DateInterval object gives the number of hours, 
        // but this only includes the hours that are not part of the full days. 
        // To get the total number of hours, we need to add the hours that are part of the full days, 
        // which is calculated as 'days * 24'.
        $hours = $interval->h + ($interval->days * 24);
        if ($hours >= $cache_life_in_hours) {
            $fetchFromAPI = true;
        }
    } else if (count($images) == 0) {
        $fetchFromAPI = true;
    }
    if ($fetchFromAPI) {
        $query = "SELECT api_id FROM CA_Breeds WHERE id = :id";
        $stmt = $db->prepare($query);
        $api_breed_id = "";
        try {
            $stmt->execute([":id" => $breed_id]);
            $result = $stmt->fetch();
            if ($result && isset($result["api_id"])) {
                $api_breed_id = $result["api_id"];
            }
        } catch (PDOException $e) {
            error_log("Error looking up breed id: " . var_export($e, true));
        }
        if(!empty($api_breed_id)){
            $refresh = count($images) === 0;
            $images = get_images_from_api_by_breed($api_breed_id);
            _store_images($images);
            if($refresh && count($images) > 0){
                $retries--;
                return get_images_by_breed_id($breed_id, $random, $retries);
            }
        }
    }
    return $images;
}

function validate_cat($cat){
    error_log("cat: " . var_export($cat, true));
    $name = se($cat, "name", "", false);
    $has_error = false;
    // name rules
    if(empty($name)){
        flash("Name is required", "warning");
        $has_error = true;
    }
    if(strlen($name) < 2){
        flash("Name must be at least 2 characters", "warning");
        $has_error = false;
    }
    //breed_id
    $breed = (int)se($cat, "breed_id", 0, false);
    if($breed === 0){
        flash("Must select a valid breed", "warning");
        $has_error = false;
    }
    //breed_extra is optional
    //sex
    $sex = se($cat, "sex", "", false);
    if(empty($sex)){
        flash("A sex must be selected", "warning");
        $has_error = true;
    }
    if(!in_array($sex, ["f","m"])){
        flash("Must select a valid sex option", "warning");
        $has_error = true;
    }
    //fixed is a boolean so we can likely ignore
    //born I'm not concerned with this value, but you may need to validate your dates (like min/max)
    //weight
    $weight = (float)se($cat, "weight", -1, false);
    if($weight == -1){
        flash("Weight must be entered", "warning");
        $has_error = true;
    }
    else if($weight < 0){
        flash("Weight must be a positive value", "warning");
        $has_error = true;
    }
    //description
    $desc = se($cat, "description", "", false);
    if(empty($desc)){
        flash("Description is required", "warning");
        $has_error = false;
    }
    else if(strlen($desc) < 2){
        flash("Description needs to be at least 2 characters", "warning");
        $has_error = true;
    }
    return !$has_error;
}