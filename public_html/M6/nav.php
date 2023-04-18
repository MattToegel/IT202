<?php

require(__DIR__ . "/../../lib/db.php");
require(__DIR__ . "/../../lib/functions.php");

?>
<nav>
    <ul>
        <li><a href="dynamic_create.php">Create Sample</a></li>
        <li><a href="dynamic_list.php">View Samples</a></li>
        <!-- don't do it this way, you can't hard code an id like this. This is just for a working example-->
        <li><a href="dynamic_edit.php?id=1">Edit Sample id 1</a></li>
    </ul>
</nav>
<style>
    ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    li {
        float: left;
        padding: 1em;
    }

    li a {
        display: block;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 1em;
    }

    input {
        width: 100%;
        display: inline-block;
    }

    h3 {
        text-align: center;
    }

    label {
        text-transform: capitalize;
    }

    form {
        width: 30%;
        margin-left: auto;
        margin-right: auto;
        margin-top: 2em;
    }
</style>