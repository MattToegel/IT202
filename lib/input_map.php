<?php

function input_map($fieldType)
{
    if (str_contains($fieldType, "varchar")) { //needed since varchars have a size
        return "text";
    } else if ($fieldType === "text") {
        return "textarea";
    } else if (in_array($fieldType, ["int", "decimal"])) { //TODO fill in as needed
        return "number";
    }
    return "text"; //default
}
