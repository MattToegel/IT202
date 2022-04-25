<?php

function update_participants($comp_id)
{
    $db = getDB();
    $stmt = $db->prepare("UPDATE RM_Competitions set current_participants = (SELECT IFNULL(COUNT(1),0) FROM RM_UserComps WHERE competition_id = :cid), 
    current_reward = IF(join_cost > 0, current_reward + CEILING(join_cost * 0.5), current_reward) WHERE id = :cid");
    try {
        $stmt->execute([":cid" => $comp_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Update competition participant error: " . var_export($e, true));
    }
    return false;
}

function add_to_competition($comp_id, $user_id)
{
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO RM_UserComps (user_id, competition_id) VALUES (:uid, :cid)");
    try {
        $stmt->execute([":uid" => $user_id, ":cid" => $comp_id]);
        update_participants($comp_id);
        return true;
    } catch (PDOException $e) {
        error_log("Join Competition error: " . var_export($e, true));
    }
    return false;
}
function join_competition($comp_id, $user_id, $cost)
{
    $balance = get_account_balance();
    if ($comp_id > 0) {
        if ($balance >= $cost) {
            $db = getDB();
            $stmt = $db->prepare("SELECT title, join_cost from RM_Competitions where id = :id");
            try {
                $stmt->execute([":id" => $comp_id]);
                $r = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($r) {
                    $cost = (int)se($r, "join_cost", 0, false);
                    $name = se($r, "title", "", false);
                    if ($balance >= $cost) {
                        if (give_gems($cost, "join-comp", get_user_account_id(), -1, "Joining competition $name")) {
                            if (add_to_competition($comp_id, $user_id)) {
                                flash("Successfully joined $name", "success");
                            }
                        } else {
                            flash("Failed to pay for competition", "danger");
                        }
                    } else {
                        flash("You can't afford to join this competition", "warning");
                    }
                }
            } catch (PDOException $e) {
                error_log("Comp lookup error " . var_export($e, true));
                flash("There was an error looking up the competition", "danger");
            }
        } else {
            flash("You can't afford to join this competition", "warning");
        }
    } else {
        flash("Invalid competition, please try again", "danger");
    }
}


function get_top_scores_for_comp($comp_id, $limit = 10)
{
    $db = getDB();
    //below if a user can win more than one place
    /*$stmt = $db->prepare(
        "SELECT score, s.created, username, u.id as user_id FROM RM_Scores s 
    JOIN RM_UserComps uc on uc.user_id = s.user_id 
    JOIN RM_Competitions c on c.id = uc.competition_id
    JOIN Users u on u.id = s.user_id WHERE c.id = :cid AND s.score >= c.min_score AND s.created 
    BETWEEN uc.created AND c.expires ORDER BY s.score desc LIMIT :limit"
    );*/
    //Below if a user can't win more than one place
    $stmt = $db->prepare("SELECT * FROM 
    (SELECT s.user_id, s.score,s.created, a.id as account_id, DENSE_RANK() OVER (PARTITION BY s.user_id ORDER BY s.score desc) as `rank` FROM RM_Scores s
    JOIN RM_UserComps uc on uc.user_id = s.user_id
    JOIN RM_Competitions c on uc.competition_id = c.id
    JOIN RM_Accounts a on a.user_id = s.user_id
    WHERE c.id = :cid AND s.created BETWEEN uc.created AND c.expires
    )as t where `rank` = 1 ORDER BY score desc LIMIT :limit");
    $scores = [];
    try {
        $stmt->bindValue(":cid", $comp_id, PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $scores = $r;
        }
    } catch (PDOException $e) {
        flash("There was a problem fetching scores, please try again later", "danger");
        error_log("List competition scores error: " . var_export($e, true));
    }
    return $scores;
}

//snippet from my functions.php
function calc_winners()
{
    $db = getDB();
    error_log("Starting winner calc");
    $calced_comps = [];
    $stmt = $db->prepare("select c.id,c.title, first_place, second_place, third_place, current_reward 
    from RM_Competitions c JOIN RM_Payout_Options po on c.payout_option = po.id 
    where expires <= CURRENT_TIMESTAMP() AND did_calc = 0 AND current_participants >= min_participants LIMIT 10");
    try {
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $rc = $stmt->rowCount();
            error_log("Validating $rc comps");
            foreach ($r as $row) {
                $fp = floatval(se($row, "first_place", 0, false) / 100);
                $sp = floatval(se($row, "second_place", 0, false) / 100);
                $tp = floatval(se($row, "third_place", 0, false) / 100);
                $reward = (int)se($row, "current_reward", 0, false);
                $title = se($row, "title", "-", false);
                $fpr = ceil($reward * $fp);
                $spr = ceil($reward * $sp);
                $tpr = ceil($reward * $tp);
                $comp_id = se($row, "id", -1, false);

                try {
                    $r = get_top_scores_for_comp($comp_id, 3);
                    if ($r) {
                        $atleastOne = false;
                        foreach ($r as $index => $row) {
                            $aid = se($row, "account_id", -1, false);
                            $score = se($row, "score", 0, false);
                            $user_id = se($row, "user_id", -1, false);
                            if ($index == 0) {
                                if (give_gems($fpr, "won-comp", -1, $aid, "First place in $title with score of $score")) {
                                    $atleastOne = true;
                                }
                                error_log("User $user_id First place in $title with score of $score");
                            } else if ($index == 1) {
                                if (give_gems($spr, "won-comp", -1, $aid, "Second place in $title with score of $score")) {
                                    $atleastOne = true;
                                }
                                error_log("User $user_id Second place in $title with score of $score");
                            } else if ($index == 2) {
                                if (give_gems($tpr, "won-comp", -1, $aid, "Third place in $title with score of $score")) {
                                    $atleastOne = true;
                                }
                                error_log("User $user_id Third place in $title with score of $score");
                            }
                        }
                        if ($atleastOne) {
                            array_push($calced_comps, $comp_id);
                        }
                    } else {
                        error_log("No eligible scores");
                    }
                } catch (PDOException $e) {
                    error_log("Getting winners error: " . var_export($e, true));
                }
            }
        } else {
            error_log("No competitions ready");
        }
    } catch (PDOException $e) {
        error_log("Getting Expired Comps error: " . var_export($e, true));
    }
    //closing calced comps
    if (count($calced_comps) > 0) {
        $query = "UPDATE RM_Competitions set did_calc = 1 AND did_payout = 1 WHERE id in ";
        $query = "(" . str_repeat("?,", count($calced_comps) - 1) . "?)";
        error_log("Close query: $query");
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($calced_comps);
            $updated = $stmt->rowCount();
            error_log("Marked $updated comps complete and calced");
        } catch (PDOException $e) {
            error_log("Closing valid comps error: " . var_export($e, true));
        }
    } else {
        error_log("No competitions to calc");
    }
    //close invalid comps
    $stmt = $db->prepare("UPDATE RM_Competitions set did_calc = 1 WHERE expires <= CURRENT_TIMESTAMP() AND current_participants < min_participants AND did_calc = 0");
    try {
        $stmt->execute();
        $rows = $stmt->rowCount();
        error_log("Closed $rows invalid competitions");
    } catch (PDOException $e) {
        error_log("Closing invalid comps error: " . var_export($e, true));
    }
    error_log("Done calc winners");
}
