<?php

$ini = @parse_ini_file(".env");
if($ini && isset($ini["DB_URL"])){
    //load local .env file
    $db_url = parse_url($ini["DB_URL"]);
}
else{
    //load from heroku env variables
    $db_url      = parse_url(getenv("DB_URL"));
}
//attempts to handle a failure where parse_url doesn't parse properly (usually happens when special characters are included)
if (!$db_url || count($db_url) === 0) {
    $matches;
    $pattern = "/mysql:\/\/(\w+):(\w+)@([^:]+):(\d+)\/(\w+)/i";
    preg_match($pattern, $ini["DB_URL"], $matches);
    $db_url["host"] = $matches[3];
    $db_url["user"] = $matches[1];
    $db_url["pass"] = $matches[2];
    $db_url["path"] = "/" . $matches[5];
}
$dbhost   = $db_url["host"];
$dbuser = $db_url["user"];
$dbpass = $db_url["pass"];
$dbdatabase       = substr($db_url["path"],1);
?>
