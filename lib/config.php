<?php

$ini = @parse_ini_file(".env");

if($ini && isset($ini["DB_URL"])){
    //load local .env file
    $url = $ini["DB_URL"];
    $db_url = parse_url($url);
}
else{
    //load from heroku env variables
    $url = getenv("DB_URL");
    $db_url = parse_url($url);
    
}
//attempts to handle a failure where parse_url doesn't parse properly (usually happens when special characters are included)
if (!$db_url || count($db_url) === 0) {
    $matches;
    $pattern = "/mysql:\/\/(\w+):(\w+)@([^:]+):(\d+)\/(\w+)/i";
    preg_match($pattern, $url, $matches);
    $db_url["host"] = $matches[3];
    $db_url["user"] = $matches[1];
    $db_url["pass"] = $matches[2];
    $db_url["path"] = "/" . $matches[5];
}
if(!$db_url || count($db_url) === 0){
    error_log("
    Failed to load environment variables.
    If this is localhost ensure the .env file is created, in the proper location, has content, and is saved.
    If this is deployed ensure your platform's environment/config variables are set.
        On Heroku that'd be under the VM/Dyno's Settings -> Reveal Config Vars
    ");

    throw new Exception("Config parsing error, check the logs for further details");
}
else{
    $dbhost   = $db_url["host"];
    $dbuser = $db_url["user"];
    $dbpass = $db_url["pass"];
    $dbdatabase       = substr($db_url["path"],1);
}
?>
