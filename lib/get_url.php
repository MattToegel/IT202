<?php
function get_url($dest, $echo = false)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    $path = "$BASE_PATH/$dest";
    if($echo){
        echo $path;
        return;
    }
    return $path;
}
