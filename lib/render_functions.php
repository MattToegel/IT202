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

function render_cat_list_item($data)
{
    include(__DIR__ . "/../partials/cat_card.php");
}

function render_stars($data = array())
{
    include(__DIR__ . "/../partials/stars.php");
}

function render_like($data = array())
{
    include(__DIR__ . "/../partials/like.php");
}
