<?php

/**
 * Will fetch the account of the logged in user, or create a new one if it doesn't exist yet.
 * Exists here so it may be called on any desired page and not just login
 * Will populate/refresh $_SESSION["user"]["account"] regardless.
 * Make sure this is called after the session has been set
 */
function get_or_create_account()
{
    if (is_logged_in()) {
        //let's define our data structure first
        //id is for internal references, account_number is user facing info, and balance will be a cached value of activity
        $account = ["id" => -1, "account_number" => false, "balance" => 0];
        //this should always be 0 or 1, but being safe
        $query = "SELECT id, account, balance from RM_Accounts where user_id = :uid LIMIT 1";
        $db = getDB();
        $stmt = $db->prepare($query);
        $created = false;
        try {
            $stmt->execute([":uid" => get_user_id()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //account doesn't exist, create it
                try {
                    //my table should automatically create the account number so I just need to assign the user
                    $query = "INSERT INTO RM_Accounts (user_id) VALUES (:uid)";
                    $user_id = get_user_id(); //caching a reference
                    $stmt = $db->prepare($query);
                    $stmt->execute([":uid" => $user_id]);
                    $account["id"] = $db->lastInsertId();
                    //this should mimic what's happening in the DB without requiring me to fetch the data
                    $account["account_number"] = str_pad($user_id, 12, "0");
                    flash("Welcome! Your account has been created successfully", "success");
                    if (give_gems(10, "welcome", -1, $account["id"], "Welcome bonus!")) {
                        flash("Enjoy 10 bonus gems as a welcome bonus!", "success");
                    }
                    $created = true;
                } catch (PDOException $e) {
                    flash("An error occurred while creating your account", "danger");
                    error_log(var_export($e, true));
                }
            } else {
                //$account = $result; //just copy it over
                $account["id"] = $result["id"];
                $account["account_number"] = $result["account"];
                $account["balance"] = $result["balance"];
            }
        } catch (PDOException $e) {
            flash("Technical error: " . var_export($e->errorInfo, true), "danger");
        }
        $_SESSION["user"]["account"] = $account; //storing the account info as a key under the user session
        if (isset($created) && $created) {
            refresh_account_balance();
        }
        //Note: if there's an error it'll initialize to the "empty" definition around line 42

    } else {
        flash("You're not logged in", "danger");
    }
}
