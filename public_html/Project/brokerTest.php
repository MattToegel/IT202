<?php
// example data
$brokers = [
    [
        "name" => "Broker A",
        "stocks" => [
            ["symbol" => "AAPL", "price" => 160.50, "low" => 158.00, "change" => 2.50, "volume" => 20567300, "shares" => 100, "historical_change" => 3.0]
        ],
        "rarity" => 1
    ],
    [
        "name" => "Broker B",
        "stocks" => [
            ["symbol" => "MSFT", "price" => 421.44, "low" => 417.84, "change" => -3.13, "volume" => 17861855, "shares" => 1, "historical_change" => 4.0],
            ["symbol" => "GOOGL", "price" => 2800.00, "low" => 2780.00, "change" => 20.00, "volume" => 1500000, "shares" => 1, "historical_change" => 25.0]
        ],
        "rarity" => 2
    ],
    [
        "name" => "Broker C",
        "stocks" => [
            ["symbol" => "NFLX", "price" => 350.00, "low" => 345.00, "change" => 5.00, "volume" => 7000000, "shares" => 1, "historical_change" => 6.0],
            ["symbol" => "DIS", "price" => 110.00, "low" => 105.00, "change" => 5.00, "volume" => 5000000, "shares" => 1, "historical_change" => 4.0],
            ["symbol" => "TSLA", "price" => 800.00, "low" => 790.00, "change" => -10.00, "volume" => 14200000, "shares" => 1, "historical_change" => 12.0]
        ],
        "rarity" => 3
    ],
    [
        "name" => "Broker D",
        "stocks" => [
            ["symbol" => "AMZN", "price" => 3100.00, "low" => 3075.00, "change" => 25.00, "volume" => 3000000, "shares" => 1, "historical_change" => 28.0],
            ["symbol" => "ORCL", "price" => 65.00, "low" => 60.00, "change" => 5.00, "volume" => 10000000, "shares" => 1, "historical_change" => 4.5],
            ["symbol" => "IBM", "price" => 130.00, "low" => 128.00, "change" => 2.00, "volume" => 3500000, "shares" => 1, "historical_change" => 3.2],
            ["symbol" => "INTC", "price" => 48.00, "low" => 47.50, "change" => 0.50, "volume" => 8000000, "shares" => 1, "historical_change" => 2.0]
        ],
        "rarity" => 4
    ],
    [
        "name" => "Broker E",
        "stocks" => [
            ["symbol" => "BABA", "price" => 210.00, "low" => 205.00, "change" => 5.00, "volume" => 15000000, "shares" => 1, "historical_change" => 6.0],
            ["symbol" => "JD", "price" => 75.00, "low" => 73.00, "change" => 2.00, "volume" => 9000000, "shares" => 1, "historical_change" => 3.5],
            ["symbol" => "PDD", "price" => 100.00, "low" => 98.00, "change" => 2.00, "volume" => 5000000, "shares" => 1, "historical_change" => 2.8],
            ["symbol" => "V", "price" => 220.00, "low" => 215.00, "change" => 5.00, "volume" => 4000000, "shares" => 1, "historical_change" => 4.7],
            ["symbol" => "MA", "price" => 330.00, "low" => 325.00, "change" => 5.00, "volume" => 2000000, "shares" => 1, "historical_change" => 5.2]
        ],
        "rarity" => 5
    ]
];

// Base stats for a Broker
$base_life = 100;
$base_defense = 10;
$base_power = 20;


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


foreach ($brokers as $broker) {
    $life = calculate_life($broker["stocks"], $base_life, $broker["rarity"]);
    $defense = calculate_defense($broker["stocks"], $base_defense, $broker["rarity"]);
    $power = calculate_power($broker["stocks"], $base_power, $broker["rarity"]);
    $stonks = calculate_stonks($life, $defense, $power, $broker["rarity"]);
    echo "Stats for " . $broker["name"] . ":\n";
    echo "Life: $life, Defense: $defense, Power: $power, stonks: $stonks\n\n";
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


$broker1 = [
    "id" => 1,
    "name" => "Broker A",
    "life" => calculate_life($brokers[0]["stocks"], $base_life, $brokers[0]["rarity"]),
    "defense" => calculate_defense($brokers[0]["stocks"], $base_defense, $brokers[0]["rarity"]),
    "power" => calculate_power($brokers[0]["stocks"], $base_power, $brokers[0]["rarity"])
];

$broker2 = [
    "id" => 2,
    "name" => "Broker B",
    "life" => calculate_life($brokers[1]["stocks"], $base_life, $brokers[1]["rarity"]),
    "defense" => calculate_defense($brokers[1]["stocks"], $base_defense, $brokers[1]["rarity"]),
    "power" => calculate_power($brokers[1]["stocks"], $base_power, $brokers[1]["rarity"])
];


battle($broker1, $broker2);
