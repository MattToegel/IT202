<h1>M2 Practice</h1>
<h3><a href="index.php">Back</a></h3>

<div>
    <h3>Practice 1</h3>
    <p>How can we iterate over every other iteration in a loop?</p>
    <details>
        <summary>Example 1: Change the iterator increment:</summary>
        <p>
        <pre><code class="language-php">
    for ($i = 0; $i < 10; $i += 2) {
        echo '$i is ' . "$i";
    }
</code></pre>
        </p>
        <p>Output: <br>
            <?php
            for ($i = 0; $i < 10; $i += 2) {
                echo '$i is ' . "$i <br>";
            }
            ?>
        </p>
    </details>
    <details>
        <summary>Example 2: Modulo</summary>
        <p>
        <pre><code class="language-php">
    for ($i = 0; $i < 10; $i++) {
        if ($i % 2 === 0) {
            echo '$i is ' . "$i";
        }
    }
    </code></pre>
        </p>
        <p>Output: <br>
            <?php
            for ($i = 0; $i < 10; $i++) {
                if ($i % 2 === 0) {
                    echo '$i is ' . "$i <br>";
                }
            }
            ?>
        </p>
    </details>
    <h3>Practice 2</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 1;
switch ($x) {
    case 0:
        $x = 2;
        break;
    case 2:
        $x = 3;
        break;
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 1;
            switch ($x) {
                case 0:
                    $x = 2;
                    break;
                case 2:
                    $x = 3;
                    break;
            }
            echo $x;
            ?>
        </p>
        <p>Although the switch statement evaluates the value, there's no matching case to handle it so the value remains untouched</p>
    </details>
    <h3>Practice 3</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 1;
switch ($x) {
    case 0:
        $x = 2;
        break;
    case 2:
        $x = 3;
        break;
    default:
        $x = 4;
    break;
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 1;
            switch ($x) {
                case 0:
                    $x = 2;
                    break;
                case 2:
                    $x = 3;
                    break;
                default:
                    $x = 4;
                    break;
            }
            echo $x;
            ?>
        </p>
        <p>In this case, the default case is triggered since no others matched</p>
    </details>
    <h3>Practice 4</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 0;
switch ($x) {
    case 0:
        $x = 2;
        break;
    case 2:
        $x = 3;
        break;
    default:
        $x = 4;
    break;
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 0;
            switch ($x) {
                case 0:
                    $x = 2;
                    break;
                case 2:
                    $x = 3;
                    break;
                default:
                    $x = 4;
                    break;
            }
            echo $x;
            ?>
        </p>
        <p>During a switch/case statement a case is only triggered once so it'll only get the value from case 0 then it'll break (exit) the flow control.</p>
    </details>
    <h3>Practice 5</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 0;
switch ($x) {
    case 0:
        $x = 2;
    case 2:
        $x = 3;
        break;
    default:
        $x = 4;
    break;
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 0;
            switch ($x) {
                case 0:
                    $x = 2;
                case 2:
                    $x = 3;
                    break;
                default:
                    $x = 4;
                    break;
            }
            echo $x;
            ?>
        </p>
        <p>During a switch/case statement a case is only triggered once so it'll only get the value from case 0, however, since the break statement is missing it'll cascade to the next case (without evaluating it again) until a break statement is met.</p>
    </details>
    <h3>Practice 6</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 11;
if($x > 5){
    $x = 10;
}
else if($x > 10){
    $x = 20;
}
else{
    $x = 0;
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 11;
            if ($x > 5) {
                $x = 10;
            } else if ($x > 10) {
                $x = 20;
            } else {
                $x = 0;
            }
            echo $x;
            ?>
        </p>
        <p>Since an if/else if block only evaluates until a truthy condition is met, the if condition will always trigger for any value above 5. The else if condition here is bad logic as it'll never evaluate.</p>
    </details>
    <h3>Practice 7</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 0;
for ($i = 0; $i < 123123; $i++) {
    $x++;
    if ($x % 2 === 0) {
        $x--;
    }
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 0;
            for ($i = 0; $i < 123123; $i++) {
                $x++;
                if ($x % 2 === 0) {
                    $x--;
                }
            }
            echo $x;
            ?>
        </p>
        <p>Typically when there's a crazy number in a problem I present, there's a trick/logic to solve it. I do not expect, nor want, you to evaluate it iteration by iteration.</p>
        <p>In this one the answer is always going to be 1 no matter what since every time $x becomes 2 (even) it'll be subtracted by 1.</p>
    </details>
    <h3>Practice 8</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
$x = 0;
for ($i = 0; $i < 123124; $i++) {
    $x++;
    if ($i % 2 === 0) {
        $x--;
    }
}
echo $x;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = 0;
            for ($i = 0; $i < 123124; $i++) {
                $x++;
                if ($i % 2 === 0) {
                    $x--;
                }
            }
            echo $x;
            ?>
        </p>
        <p>Typically when there's a crazy number in a problem I present, there's a trick/logic to solve it. I do not expect, nor want, you to evaluate it iteration by iteration.</p>
        <p>In this version of the problem, $x was swapped for $i in the if condition. So every time $i was even, 1 would be subtracted. So effectively this is the same as dividing the max value by 2 since only half of the iterations will be staying incremented.</p>
    </details>
    <h3>Practice 9</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
echo "1" + 5;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = "1" + 5;
            var_dump($x);
            ?>
        </p>
        <p>In this sample, at least with this version of php, the string of 1 gets casted to an int during the math operation.</p>
    </details>
    <h3>Practice 10</h3>
    <p>What is the output of the following snippet?<br>
    <pre><code class="language-php">
echo "1" . 5;
</code></pre>
    <details>
        <summary>Answer</summary>
        <p>$x =
            <?php
            $x = "1" . 5;

            var_dump($x);
            ?>
        </p>
        <p>In php the . operator is concatenation so it'll connect the two values as strings.</p>
    </details>
</div>
<?php //ignore this, it's for effects on the code
require(__DIR__ . "/../../partials/prism.php");
?>