<?php
function activate_item($item_id)
{
    //refer to Project/sql/011_insert_items.sql
    /**
     * (-1, "Rope", "Saves from one Pit Fall", 9999999, 5, ""),
(-2, "Wolf Deterent 25%", "Has a chance to prevent a wolf from appearing during a game session", 9999999, 15, ""),
(-3, "First Aid Kit 1", "Gain some extra points for the next rescued friend", 9999999, 1, ""),
(-4, "First Aid Kit 2", "Gain moderate extra points for the next rescued friend", 9999999, 2, ""),
(-5, "First Aid Kit 3", "Gain large amount of extra points for the next rescued friend", 9999999, 5, ""),
(-6, "Wolf Deterent 50%", "Has a chance to prevent a wolf from appearing during a game session",9999999, 25,"")
     */
    switch ($item_id) {
        case -1:
            $_SESSION["rope"] = 1;
            break;
        case -2:
            $_SESSION["deterent"] = .25;
            break;
        case -3:
            $_SESSION["first_aid"] = .5;
            break;
        case -4:
            $_SESSION["first_aid"] = .75;
            break;
        case -5:
            $_SESSION["first_aid"] = 1.0;
            break;
        case -6:
            $_SESSION["deterent"] = .5;
            break;
        default:
            error_log("Unhandled item_id $item_id");
            break;
    }
}
function get_friend_value($original)
{
    $mod = (float)se($_SESSION["first_aid"], 0, false);
    if ($mod > 0) {
        $original += ($original * $mod);
        unset($_SESSION["first_aid"]);
    }
    return $original;
}
