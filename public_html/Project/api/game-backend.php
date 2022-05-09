<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("game_backend received data: " . var_export($_REQUEST, true));
//cell template for structure/defaults
$cell_data = [
    "type" => "",
    "x" => 0,
    "y" => 0,
    "adj" => [],
    "visited" => false
];
if (isset($_POST["type"])) {
    $type = $_POST["type"];
    error_log("type: " . $type);
    if ($type === "load") {
        $resp = load_game();
        echo json_encode($resp);
        error_log(var_export($resp, true));
        die();
    } else if ($type === "move") {
        $dx = (int)se($_POST, "x", 0, false);
        $dy = (int)se($_POST, "y", 0, false);
        $cell = move($dx, $dy);
        if (isset($cell["event"])) {
            echo json_encode($cell);
            die();
        }
        //$resp = load_game();
        //echo json_encode($resp);
        echo json_encode($cell);
        die();
    } else if ($type === "check") {
        $x = (int)se($_POST, "x", 0, false);
        $y = (int)se($_POST, "y", 0, false);
        $cell = check_cell($x, $y);
        $cell["visited"] = true;
        error_log(var_export($cell, true));
        echo json_encode($cell);
        die();
    } else if ($type === "active_items") {
        $resp = ["items" => get_active_items()];
        echo json_encode($resp);
        die();
    }
}
function check_cell($x, $y)
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (isset($_SESSION["game"])) {
        $game = $_SESSION["game"];


        //adjs
        $adjs = [];
        $rows = $game["rows"];
        $cols = $game["cols"];
        error_log("checking coord: $x,$y $rows x $cols");
        if ($x >= 0 && $x < $cols && $y >= 0 && $y < $rows) {
            $cell = $game["cells"][$y][$x];

            error_log("checking " . var_export($cell, true));
            if (!$cell["visited"]) {
                error_log("not visted");
                $cell["visited"] = true;
                //check current cell
                $type = $cell["type"];
                error_log("cell type $type");
                if ($type == "W") {
                    $_SESSION["game"] = null;
                    require_once(__DIR__ . "/save_score.php");
                    save_score($game["player"]["score"], $game["level"], $game["player"]["friends"], false);
                    unset($_SESSION["game"]);
                    clear_deterrent();
                    return ["event" => "died", "reason" => "Eaten by wolf"];
                } else if ($type == "P") {
                    if (!has_rope()) {
                        $_SESSION["game"] = null;
                        require_once(__DIR__ . "/save_score.php");
                        save_score($game["player"]["score"], $game["level"], $game["player"]["friends"], false);
                        unset($_SESSION["game"]);
                        clear_deterrent();
                        return ["event" => "died", "reason" => "Fell in a pit"];
                    } else {
                        $cell["extra"] = "A rope saved you from falling";
                    }
                } else if ($type == "L") {
                    $game["player"]["score"] += 1000;
                    $game["player"]["x"] = 0;
                    $game["player"]["y"] = 0;
                    $game["pending_next"] = true;
                    $_SESSION["game"] = $game;
                    return ["event" => "next_level", "reason" => ""];
                } else if ($type == "F") {
                    $cell["type"] = "R";

                    $cell["friend"] = rand(0, 2); //update if friend count changes


                    //TODO add potential modifier
                    $cell["score"] = get_friend_value(500);
                    error_log("Friend Score: " . $cell["score"]);
                    $game["player"]["score"] += $cell["score"];
                    $game["player"]["friends"]++;
                } else if ($type == "R") {
                    //unset($cell["score"]);
                }
                //check adjacent cells
                $up = $y - 1;
                $down = $y + 1;
                $left = $x - 1;
                $right = $x + 1;
                //swapping Y check since our 0,0 is top left
                //residual from the js design
                if ($down < $rows) {
                    $c = $game["cells"][$down][$x];
                    if (strlen($c["type"]) > 0) {
                        array_push($adjs, $c["type"]);
                    }
                }
                if ($up >= 0) {
                    $c = $game["cells"][$up][$x];
                    if (strlen($c["type"]) > 0) {
                        array_push($adjs, $c["type"]);
                    }
                }
                if ($left >= 0) {
                    $c = $game["cells"][$y][$left];
                    if (strlen($c["type"]) > 0) {
                        array_push($adjs, $c["type"]);
                    }
                }
                if ($right < $cols) {
                    $c = $game["cells"][$y][$right];
                    if (strlen($c["type"]) > 0) {
                        array_push($adjs, $c["type"]);
                    }
                }
                if (count($adjs) > 0) {
                    $adjs = array_unique($adjs);
                }
                $cell["adj"] = $adjs;

                $game["cells"][$y][$x] = $cell;
                error_log("saving cell " . var_export($game["cells"][$y][$x], true));
                $_SESSION["game"] = $game;
            }

            return $cell;
        }
        return false;
    }
}
function move($x, $y)
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION["game"])) {
        $game = $_SESSION["game"];
        $player = $game["player"];
        $px = $player["x"];
        $py = $player["y"];
        $px += $x;
        $py += $y;
        $moved = true;
        if ($px < 0) {
            $px = 0;
            $moved = false;
        } else if ($px >= $game["cols"]) {
            $px = $game["cols"] - 1;
            $moved = false;
        }
        if ($py < 0) {
            $py = 0;
            $moved = false;
        } else if ($py >= $game["rows"]) {
            $py = $game["rows"] - 1;
            $moved = false;
        }
        $moves = $player["moves"];
        if ($moved) {
            $moves++;
        }
        error_log(var_export($player, true));
        //TODO check new cell if moved
        $cell = check_cell($px, $py); //gets mapped to game internally
        if (!isset($cell["event"]) && !isset($cell["next_level"])) {
            $game = $_SESSION["game"];
            $player = $game["player"];
            $player["x"] = $px;
            $player["y"] = $py;
            $player["moves"] = $moves;

            $game["player"] = $player;
            $_SESSION["game"] = $game;
        }
        return $cell;
    }
}
function generate_level($level = 1)
{
    $level_mod = ((int)floor($level / 5) + 3);
    $rows = $level_mod;
    $cols = $level_mod;
    error_log("$rows x $cols");
    $total = $rows * $cols;
    $pit_count = ceil($total * .1);
    $friends = ceil($total * .05);
    $wolf = 1;
    $ladder = 1;
    $cells = [];
    //add pits
    for ($i = 0; $i < $pit_count; $i++) {
        array_push($cells, "P");
    }
    //add friends
    for ($i = 0; $i < $friends; $i++) {
        array_push($cells, "F");
    }
    //add wolf
    for ($i = 0; $i < $wolf; $i++) {
        if (!deter_wolf()) {
            array_push($cells, "W");
        } else {
            error_log("Wolf detered");
        }
    }
    //add ladder
    for ($i = 0; $i < $ladder; $i++) {
        array_push($cells, "L");
    }
    $cell_count = count($cells);
    //fill unassigned cells
    if ($cell_count < ($total - 1)) {
        $diff = $total - $cell_count - 1;
        for ($i = 0; $i < $diff; $i++) {
            array_push($cells, "");
        }
    }
    global $cell_data;
    shuffle($cells);
    //game template
    if ($level === 1) {
        $game = [
            "level" => $level,
            "rows" => $rows,
            "cols" => $cols,
            "player" => [
                "x" => 0,
                "y" => 0,
                "moves" => 0,
                "score" => 0,
                "friends" => 0
            ],
        ];
    } else {
        $game = $_SESSION["game"];
        $game["level"] = $level;
        $game["rows"] = $rows;
        $game["cols"] = $cols;
        $game["player"]["x"] = 0;
        $game["player"]["y"] = 0;
    }
    //https://www.php.net/manual/en/function.array-fill.php
    $game["cells"] =
        array_fill(0, $rows, array_fill(0, $cols, 0));
    error_log("Grid: " . var_export($game, true));
    error_log("Cell: " . var_export($cells, true));
    for ($y = 0; $y < $rows; $y++) {
        for ($x = 0; $x < $cols; $x++) {
            $cell = $cell_data;
            $cell["x"] = $x;
            $cell["y"] = $y;
            if ($x === 0 && $y === 0) {
                $cell["type"] = "";
            } else {
                //https://www.w3schools.com/php/func_array_shift.asp
                $cell["type"] = array_shift($cells);
            }
            $game["cells"][$y][$x] = $cell;
        }
    }
    error_log("Finished Grid: " . var_export($game, true));
    $_SESSION["game"] = $game;
    return $game;
    /* example
    $game = [
        "level" => 1,
        "rows" => 4,
        "columns" => 4,
        "player" => [
            "x" => 0,
            "y" => 0,
            "moves" => 0,
            "score" => 0,
            "friends" => 0
        ],
        "cells" => [
            [
                "x" => 1,
                "y" => 1,
                "type" => "",
                "adj" => [],
                "visited" => false
            ]
        ]
    ];*/
}
function load_game()
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION["game"]) && !is_null($_SESSION["game"])) {
        //TODO limit info shared to client side

        //grid dimensions
        //visited cell data
        //adj cell data (if generated)
        $game = $_SESSION["game"];
        if (isset($game["pending_next"])) {
            error_log("generating next level");
            $level = $game["level"];
            $level++;
            $game = generate_level($level);
            unset($game["pending_next"]);
        } else {
            error_log("loading game");
        }
    } else {
        error_log("generating new game");
        load_active_items();
        //generate
        $level = 1;
        //level % 5 == 0 (+1)
        $game = generate_level($level);
    }
    $px = $game["player"]["x"];
    $py = $game["player"]["y"];
    $cell = check_cell($px, $py);
    $game["cells"][$py][$px] = $cell;
    global $cell_data;
    //convert to response
    $resp = [
        "level" => $game["level"],
        "player" => $game["player"],
        "rows" => $game["rows"],
        "cols" => $game["cols"],
        //fill as blank, then only update what the client's visited or knows about
        "cells" => array_fill(0, $game["rows"], array_fill(0, $game["cols"], $cell_data))
    ];
    for ($y = 0; $y < $game["cols"]; $y++) {
        for ($x = 0; $x < $game["rows"]; $x++) {
            $gCell = $game["cells"][$y][$x];
            if ($gCell["visited"] || count($gCell["adj"]) > 0) {
                $resp["cells"][$y][$x] = $gCell;
            } else {
                $resp["cells"][$y][$x]["x"] = $x;
                $resp["cells"][$y][$x]["y"] = $y;
            }
        }
    }
    $_SESSION["game"] = $game;
    return $resp;
}
//load_game();
