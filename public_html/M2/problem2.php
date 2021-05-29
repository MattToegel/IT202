<?php
$a1 = [10.001, 11.591, 0.011, 5.991, 16.121, 0.131, 100.981, 1.001];
$a2 = [1.99, 1.99, 0.99, 1.99, 0.99, 1.99, 0.99, 0.99];
$a3 = [0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01];
function getTotal($arr) {
    $total = 0.00;
    //TODO do adding here
    //TODO do rounding stuff here
    return $total;
}
echo "Problem 2: Adding Floats<br>";
echo "The total of Array 1 is " . var_export(getTotal($a1), true);
echo "<br>";
echo "The total of Array 2 is " . var_export(getTotal($a2), true);
echo "<br>";
echo "The total of Array 3 is " . var_export(getTotal($a3), true);
