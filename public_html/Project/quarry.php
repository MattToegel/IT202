<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
/*
This is the primary game area.
It'll list rocks owned with their status (mining, ready, etc)

Since this may be a heavy part of the application we're going to utlize a cookie to cache some data to reduce DB hits
Note: Max cookie size is 4096 bytes
*/

$rocks = [];
$forceRefresh = false;
if (isset($_COOKIE["rocks"])) {
    //Note: Don't absolutely try this data, it's merely cached for visual purposes
    $rocks = json_decode($_COOKIE["rocks"], true);
    $forceRefresh = ((int)se($_COOKIE, "refreshRocks", 0, false)) === 1; //We'll use this to force refresh (i.e, any time a significant change occurs that requires a refresh)
}
if (!isset($rocks) || empty($rocks) || $forceRefresh) {
    //fetch db
    $db = getDB();
    $query = "SELECT time_to_mine, potential_reward, percent_chance, id, opens_date, is_mining FROM Rocks WHERE opened_date is null LIMIT 100";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $rocks = $r;
        }
    } catch (PDOException $e) {
        flash("Error fetching rocks: " . var_export($e->errorInfo, true), "warning");
    }
    //create cookie

    /**
     * Logic
     * Present user with 3 random rocks
     * This list persists until a choice is picked.
     * Add picked rock to rocks table
     * Batch (id, created, user_id, made_choice)
     * Pending Rock (created, time_to_mine, potential_reward, percent_change, owned_by, chosen_date, batch_id)
     * Rock(created,modified, time_to_mine, is_mining, opens_date, potential_reward,
     * percent_chance, opened_date, given_reward, owned_by)
     */
}
?>
<?php
//show rock choices if available
$choices = [];
//find a batch choices yet to be chosen, should just be 3 since the game presents 3 options
//a voucher can't be used if there's a pending selection
$query = "SELECT p.id, p.time_to_mine, p.potential_reward, p.percent_chance from Batches b JOIN Pending_Rocks p ON b.id = p.batches_id WHERE b.user_id = :uid and b.made_choice = 0 LIMIT 3";
$db = getDB();
$stmt = $db->prepare($query);
try {
    $stmt->execute([":uid" => get_user_id()]);
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($pending) {
        $choices = $pending;
    }
} catch (PDOException $e) {
    flash("An unknown error occurs: " . var_export($e->errorInfo, true), "danger");
}
?>
<div class="container-fluid">
    <div>
        <?php if ($choices && count($choices) > 0) : ?>
            <h3>Pick a Rock</h3>
            <div class="row">
                <?php foreach ($choices as $rock) : ?>
                    <div class="col">
                        <?php /*note: $rock must be set here along with $isPotential = true*/
                        $isPotential = true; ?>
                        <?php include(__DIR__ . "/../../partials/rock-item.php"); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php if (get_vouchers() > 0) : ?>
                <button type="button" class="btn btn-primary" onclick="useVoucher()">Use Voucher (<?php se(get_vouchers()); ?>)</button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div>
        <h3>Your Rocks</h3>
        <div class="row">
            <?php if ($rocks && count($rocks) > 0) : ?>
                <?php foreach ($rocks as $rock) : ?>
                    <div class="col">
                        <?php /*note: $rock must be set here*/ ?>
                        <?php include(__DIR__ . "/../../partials/rock-item.php"); ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col">You don't have any rocks yet. Buy some vouchers at the Shop then come back here.</div>
            <?php endif; ?>
        </div>
    </div>
    <footer class="footer mt-auto py-3">
        <div>Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
    </footer>
</div>
<script>
    function pickRock(ele) {
        const id = ele.id || 0;
        if (id > 0) {
            if (!!window.jQuery === true) {
                $.get("api/pick_rock.php?id=" + id, (res) => {
                    console.log("jQuery res", res);
                    let data = JSON.parse(res);
                    if (data.status === 200) {
                        //doing a lazy reload for now (could get expensive)
                        window.location.reload();
                    } else {
                        flash(data.message, "warning");
                    }
                });
            } else {
                fetch("api/pick_rock.php?id=" + id, {
                    headers: {
                        //"Content-type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest",
                    }
                }).then(async res => {
                    console.log("fetch api resp", res);
                    let data = await res.json();
                    if (data.status === 200) {
                        //doing a lazy reload for now (could get expensive)
                        window.location.reload();
                    } else {
                        flash(data.message, "warning");
                    }
                });
            }
        }
    }

    function useVoucher() {
        //at the moment, this is a basic GET request.
        //it relies on session data for validity
        if (!!window.jQuery === true) {
            $.get("api/use_voucher.php", (res) => {
                console.log("jQuery res", res);
                let data = JSON.parse(res);
                if (data.status === 200) {
                    //doing a lazy reload for now (could get expensive)
                    window.location.reload();
                } else {
                    flash(data.message, "warning");
                }
            });
        } else {
            fetch("api/use_voucher.php", {
                headers: {
                    //"Content-type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                }
            }).then(async res => {
                console.log("fetch api resp", res);
                let data = await res.json();
                if (data.status === 200) {
                    //doing a lazy reload for now (could get expensive)
                    window.location.reload();
                } else {
                    flash(data.message, "warning");
                }
            });
        }
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>