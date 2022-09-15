<?php

/**
 * Points should be passed as a positive value.
 * $losing should be where the points are coming from
 * $gaining should be where the points are going
 */
function give_gems($gems, $reason, $losing = -1, $gaining = -1, $details = "")
{
    //I'm choosing to ignore the record of 0 point transactions
    if ($gems > 0) {
        $query = "INSERT INTO RM_Gem_History (src, dest, diff, reason, details) 
            VALUES (:acs1, :acd1, :pc, :r,:m), 
            (:acs2, :acd2, :pc2, :r, :m)";
        //I'll insert both records at once, note the placeholders that are kept the same and the ones changed.
        $params[":acs1"] = $losing;
        $params[":acd1"] = $gaining;
        $params[":r"] = $reason;
        $params[":m"] = $details;
        $params[":pc"] = ($gems * -1);

        $params[":acs2"] = $gaining;
        $params[":acd2"] = $losing;
        $params[":pc2"] = $gems;
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            //Only refresh the balance of the user if the logged in user's account is part of the transfer
            //this is needed so future features don't waste time/resources or potentially cause an error when a calculation
            //occurs without a logged in user
            refresh_account_balance($losing);
            refresh_account_balance($gaining);

            return true;
        } catch (PDOException $e) {
            error_log(var_export($e->errorInfo, true));
            flash("There was an error transfering gems", "danger");
        }
    } else if ($gems === 0) {
        error_log("Freebie purchase");
        flash("You got something for free!", "success");
        return true;
    }
    return false;
}
