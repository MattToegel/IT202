<?php

function fetch_names()
{
    $data = ["gender" => "random"];
    $endpoint = "https://nicknamegenerator.p.rapidapi.com/api/nick-names";
    $isRapidAPI = true;
    $rapidAPIHost = "nicknamegenerator.p.rapidapi.com";
    $result = get($endpoint, "NAME_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    if (isset($result["body"])) {
        $result = $result["body"];
    }
    return $result;
}
