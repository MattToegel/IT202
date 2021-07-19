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

//fetch tools
$tools = [];
$query = "SELECT name, inv.id, iname, quantity from Inventory inv JOIN Items i on inv.item_id = i.id WHERE user_id = :uid AND name like '%pickaxe' AND quantity > 0";
$db = getDB();
$stmt = $db->prepare($query);
try {
    $stmt->execute([":uid" => get_user_id()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $tools = $results;
    }
} catch (PDOException $e) {
    error_log("Error fetching tools for user " . get_user_id() . ": " . var_export($e->errorInfo, true));
}
?>
<div class="container-fluid">
    <div>
        <?php if ($choices && count($choices) > 0) : ?>
            <?php $title = "Pick a Rock";
            include(__DIR__ . "/../../partials/title.php"); ?>
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
        <?php $title = "Your Rocks";
        include(__DIR__ . "/../../partials/title.php"); ?>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 rows-cols-xl-5 row-cols-xxl-5">
            <?php if ($rocks && count($rocks) > 0) : ?>
                <?php foreach ($rocks as $rock) : ?>
                    <div class="col m-2" style="width:20em">
                        <?php /*note: $rock must be set here*/ ?>
                        <?php include(__DIR__ . "/../../partials/rock-item.php"); ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col">You don't have any rocks yet. Buy some vouchers at the Shop then come back here.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal" id="toollist" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select a Tool</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="toolForm" onsubmit="return false;">
                        <input type="hidden" name="rock_id" value="set via js" />
                        <?php foreach ($tools as $key => $tool) : ?>
                            <input type="radio" name="tool" class="form-check-input" value="<?php se($tool, "id"); ?>" id="<?php se($key); ?>" required />
                            <label class="form-check-label" for="<?php se($key); ?>">
                                <?php se($tool, "quantity", 0); ?>x - <?php se($tool, "name"); ?>
                            </label>
                        <?php endforeach; ?>
                        <?php if (!$tools || count($tools) == 0) : ?>
                            <p>You don't have any tools, you'll have to purchase some at the shop.</p>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if (!$tools || count($tools) == 0) : ?>
                        <a href="shop.php" class="btn btn-primary">Visit Shop</a>
                    <?php else : ?>
                        <button type="button" onclick="startMining()" class="btn btn-primary">Use Tool</button>
                    <?php endif;  ?>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer mt-auto py-3">
        <div>Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
    </footer>
</div>
<script>
    //https://www.w3resource.com/javascript-exercises/fundamental/javascript-fundamental-exercise-230.php
    const formatDuration = ms => {
        if (ms < 0) ms = -ms;
        const time = {
            day: Math.floor(ms / 86400000),
            hour: Math.floor(ms / 3600000) % 24,
            minute: Math.floor(ms / 60000) % 60,
            second: Math.floor(ms / 1000) % 60,
            millisecond: Math.floor(ms) % 1000
        };
        return Object.entries(time)
            .filter(val => val[1] !== 0)
            .map(val => val[1] + ' ' + (val[1] !== 1 ? val[0] + 's' : val[0]))
            .join(', ');
    };

    function diff_ms(dt2, dt1) {

        var diff = (dt2.getTime() - dt1.getTime());
        console.log("d0", dt2, "d1", dt1, "diff", diff);
        return Math.abs(Math.round(diff));

    }

    function checkReward(ele) {
        const rock_id = ele.id;
        if (rock_id > 0) {
            if (!!window.jQuery === true) {
                $.post("api/check_reward.php", {
                    rock_id: rock_id
                }, (res) => {
                    console.log("response", res);
                    let data = JSON.parse(res);
                    if (data.status == "200") {
                        flash(data.message, "success");
                        //remove the card from the dom (no need to refresh the page here as not being lazy)
                        ele.closest(".col").remove(); //want to remove the entire column
                    } else {
                        flash(data.message, "danger");
                    }
                });
            } else {
                fetch("api/check_reward.php", {
                    method: "POST",
                    headers: {
                        "Content-type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        rock_id: rock_id
                    })
                }).then(async res => {
                    console.log(res);
                    let data = await res.json();
                    console.log("fetch response", data);
                    if (data.status == "200") {
                        flash(data.message, "success");
                        //remove the card from the dom (no need to refresh the page here as not being lazy)
                        ele.closest(".col").remove(); //want to remove the entire column
                    } else {
                        flash(data.message, "danger");
                    }
                });
            }
        } else {
            flash("There was a problem finding the rock to check", "warning");
        }
    }

    function startMining() {
        let modalEl = document.getElementById("toollist");
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
        let form = document.getElementById("toolForm");
        const rock_id = form.rock_id.value;
        const tool_id = form.tool.value;
        if (!!window.jQuery === true) {
            $.post("api/mine_it.php", {
                rock_id: rock_id,
                tool_id: tool_id
            }, (res) => {
                let data = JSON.parse(res);
                console.log(data);
                flash(data.message);
                //doing a lazy reload for now (could get expensive)
                window.location.reload();
            });
        } else {
            fetch("api/mine_it.php", {
                method: "POST",
                headers: {
                    "Content-type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    rock_id: rock_id,
                    tool_id: tool_id
                })
            }).then(async res => {
                console.log(res);
                let data = await res.json();
                console.log("fetch response", data);
                if (data.status === 200) {
                    flash("Purchase Successful!", "success");
                    refreshBalance();
                } else {
                    //flash("Error occurred: " + JSON.stringify(data), "danger");
                    flash(data.message, "warning");
                }
            });
        }
    }

    function prepareMining(ele) {
        let modalEl = document.getElementById("toollist");
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        let rockIdEle = modalEl.getElementsByTagName("form")[0].rock_id;
        rockIdEle.value = ele.id;
        console.log("form", modalEl.getElementsByTagName("form")[0].rock_id);
        modal.show();
    }


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