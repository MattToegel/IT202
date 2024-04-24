<?php

function session_save($key, $value)
{
    if (isset($_SESSION["user"])) {
        $_SESSION["user"][$key] = json_encode($value);
    }
}
function session_load($key)
{
    if (isset($_SESSION["user"]) && isset($_SESSION["user"][$key])) {
        try {
            $data = json_decode($_SESSION["user"][$key], true);
            if ($data) {
                return $data;
            }
        } catch (Exception $e) {
            error_log("Error processing session load. " . $_SESSION["user"][$key]);
        }
    }
    return null;
}

function session_delete($key)
{
    if (isset($_SESSION["user"]) && isset($_SESSION["user"][$key])) {
        unset($_SESSION["user"][$key]);
    }
}
