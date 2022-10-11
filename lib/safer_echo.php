<?php

/** Safe Echo Function
 * Takes in a value and passes it through htmlspecialchars()
 * or
 * Takes an array, a key, and default value and will return the value from the array if the key exists or the default value.
 * Can pass a flag to determine if the value will immediately echo or just return so it can be set to a variable
 */
<<<<<<< HEAD
function se($v, $k = null, $default = "", $isEcho = true) {
=======
function se($v, $k = null, $default = "", $isEcho = true)
{
>>>>>>> f36639b392ddc8c018f137a45b7a85c12657ea7a
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
<<<<<<< HEAD
function safer_echo($v, $k = null, $default = "", $isEcho = true){
  return se($v, $k, $default, $isEcho);
}
=======
function safer_echo($v, $k = null, $default = "", $isEcho = true)
{
    return se($v, $k, $default, $isEcho);
}
>>>>>>> f36639b392ddc8c018f137a45b7a85c12657ea7a
