<?php
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in(){
    return isset($_SESSION["user"]);
}
function has_role($role){
    if(is_logged_in() && isset($_SESSION["user"]["roles"])){
        return in_array($role, $_SESSION["user"]["roles"]);
    }
    return false;
}
?>