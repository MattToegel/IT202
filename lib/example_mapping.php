<?php

function map_data($api_data){
    $records = [];
    foreach($api_data as $data){
        $record["name"] = $data["name"];
        $record["other_field"] = $data["other_field"];
        $record["example"] = $data["example_field_from_api"];
        array_push($records, $record);
    }
    return $records;
}