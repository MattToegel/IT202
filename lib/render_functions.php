<?php

function render_input($data = array())
{
    include(__dir__ . "/../partials/input_field.php");
}

function render_button($data = array())
{
    include(__DIR__ . "/../partials/button.php");
}

function render_table($data = array())
{
    include(__DIR__ . "/../partials/table.php");
}

function render_like($data = array()){
    include(__DIR__ . "/../partials/like.php");
}

function render_stars($data = array()){
    include(__DIR__ . "/../partials/stars.php");
}