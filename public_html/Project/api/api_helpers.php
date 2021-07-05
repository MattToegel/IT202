<?php
//These are helpers specific to API

//https://stackoverflow.com/a/48302354
/**
 * Attempts to determine if the request was invoked via ajax.
 * Note: can be spoofed so should be combined with some other method (i.e., set/check a temp session variable)
 */
function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}
