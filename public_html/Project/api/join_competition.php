<?php
//these files in the API folder aren't expected to be fully user facing.
//A user shouldn't access these directly.
//we'll be using ajax to send/receive data here
require(__DIR__ . "/api_helpers.php"); //specific helpers just for API
if (!isAjax()) {
    die(header("Location: index.php"));
}
$response = ["message" => "An error occurred", "status" => 400]; //defined a response template with initially failure data

session_start(); //since we're not pulling in nav.php we do need to explicitly ask for the session
require(__DIR__ . "/../../../lib/functions.php"); //general application helpers (i.e., pulls in db)
//Note: It's not advisable to use flash() in ajax handlers, the message will only show on the next page load
//and the timing would be off since ajax doesn't trigger a page load

if (is_logged_in()) {
    $comp_id = se($_POST, "competition_id", -1, false);
    $response["status"] = 200; //200 means ok
    $response["message"] = join_competition($comp_id);
} else {
    $response["message"] = "User must be logged in";
}
//with ajax you must be cautious with what you echo (or write) to the output buffer
//anything written will get sent as the response
//make sure you only echo just the encoded $response
echo json_encode($response);//<-- this is the "return" value to the request