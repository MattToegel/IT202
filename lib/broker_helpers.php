<?php
function normalize_volume($volume)
{
    return log1p($volume) / 10;
}
function diminishing_returns($shares)
{
    return sqrt($shares);
}


function calculate_life($stocks, $base_life, $rarity)
{
    $total_life = $base_life;
    foreach ($stocks as $stock) {
        $adjusted_shares = diminishing_returns($stock["shares"]);
        $total_life += abs($stock["change"]) * $adjusted_shares;
    }
    return max(ceil($total_life * (1 + $rarity / .5)), 1);
}


function calculate_defense($stocks, $base_defense, $rarity)
{
    $total_defense = $base_defense;
    foreach ($stocks as $stock) {
        $adjusted_shares = diminishing_returns($stock["shares"]);
        $total_defense += normalize_volume($stock["volume"]) * $adjusted_shares;
    }
    return max(ceil($total_defense * (1 + $rarity / .5)), 1);
}


function calculate_power($stocks, $base_power, $rarity)
{
    $total_power = $base_power;
    foreach ($stocks as $stock) {
        $adjusted_shares = diminishing_returns($stock["shares"]);

        $normalized_change = ($stock["change"] + $stock["historical_change"]) / 2;
        $total_power += $normalized_change * $adjusted_shares;
    }
    return max(ceil($total_power * (1 + $rarity / .75)), 1);
}


function calculate_stonks($life, $defense, $power, $rarity)
{
    $stock_count_bonus = sqrt($rarity);
    return max(ceil(($life + $defense + $power) * $stock_count_bonus), 1);
}

function calculate_broker_stats($broker)
{
    // Base stats for a Broker
    $base_life = 100;
    $base_defense = 10;
    $base_power = 20;
    $life = calculate_life($broker["stocks"], $base_life, $broker["rarity"]);
    $defense = calculate_defense($broker["stocks"], $base_defense, $broker["rarity"]);
    $power = calculate_power($broker["stocks"], $base_power, $broker["rarity"]);
    $stonks = calculate_stonks($life, $defense, $power, $broker["rarity"]);
    error_log("Stats for " . $broker["name"] . ":\n");
    error_log("Life: $life, Defense: $defense, Power: $power, stonks: $stonks\n\n");
    $broker["stats"]["life"] = $life;
    $broker["stats"]["defense"] = $defense;
    $broker["stats"]["power"] = $power;
    $broker["stats"]["stonks"] = $stonks;
    return $broker;
}

function battle($broker1, $broker2)
{

    $required_properties = ["id", "power", "defense", "life"];


    $validate_properties = function ($broker) use ($required_properties) {
        return !array_diff_key(array_flip($required_properties), $broker);
    };

    if (!$validate_properties($broker1) || !$validate_properties($broker2)) {
        echo "Error: One or both brokers are missing required properties.\n";
        return;
    }
    $rounds = 150;
    $currentRound = 0;
    $broker1_life = $broker1["life"];
    $broker2_life = $broker2["life"];
    $broker1_id = $broker1["id"];
    $broker2_id = $broker2["id"];
    $events = [];
    array_push($events, [
        "action" => "start",
        "broker1_id" => $broker1_id,
        "broker2_id" => $broker2_id,
        "broker1_life" => $broker1_life,
        "broker2_life" => $broker2_life,
        "broker1_dmg" => 0,
        "broker2_dmg" => 0,
        "round" => $currentRound
    ]);
    while ($currentRound < $rounds && $broker1_life > 0 && $broker2_life > 0) {

        $broker1_effective_power = rand((int)($broker1["power"] / 2), $broker1["power"]);
        $broker2_effective_defense = rand((int)($broker2["defense"] / 2), $broker2["defense"]);

        // Broker 1 attacks Broker 2
        $damage_to_b2 = max(1, $broker1_effective_power - $broker2_effective_defense);
        $broker2_life -= $damage_to_b2;


        $broker2_effective_power = rand((int)($broker2["power"] / 2), $broker2["power"]);
        $broker1_effective_defense = rand((int)($broker1["defense"] / 2), $broker1["defense"]);

        // Broker 2 attacks Broker 1
        $damage_to_b1 = max(1, $broker2_effective_power - $broker1_effective_defense);
        $broker1_life -= $damage_to_b1;
        array_push($events, [
            "action" => "battle",
            "broker1_id" => $broker1_id,
            "broker2_id" => $broker2_id,
            "broker1_life" => $broker1_life,
            "broker2_life" => $broker2_life,
            "broker1_dmg" => $damage_to_b2,
            "broker2_dmg" => $damage_to_b1,
            "round" => $currentRound + 1
        ]);
        $currentRound++;


        if ($broker1_life <= 0 || $broker2_life <= 0) {
            $events[count($events) - 1]["action"] = "end-ko";
            break;
        }
    }
    if ($currentRound + 1 >= $rounds) {
        $events[count($events) - 1]["action"] = "end-limit";
    }
    echo "Battle result after $currentRound rounds:\n";
    echo $broker1["name"] . " has " . max($broker1_life, 0) . " life remaining.\n";
    echo $broker2["name"] . " has " . max($broker2_life, 0) . " life remaining.\n";


    if ($broker1_life > $broker2_life) {
        echo $broker1["name"] . " wins the battle!\n";
    } elseif ($broker1_life < $broker2_life) {
        echo $broker2["name"] . " wins the battle!\n";
    } else {

        echo $broker2["name"] . " wins the battle by default on a tie.\n";
    }
    var_export($events);
}