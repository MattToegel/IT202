<h1>PHP and JS Loops Comparison</h1>
<h3><a href="index.php">Back</a></h3>

<p>In all programming languages, we have ways to iterate over data. In PHP we have 4 different iterators:</p>
<p>We have the while loop, which iterates while a condition is true:</p>
<p>
<pre><code class="language-php">
$x = 0;
while ($x < 10) {
    $x++;
    echo '$x' . " + 1 is $x";
}
</code></pre>
</p>
<p>Output: <br>
    <?php
    $x = 0;
    echo "Loop will end at 10." . '$x' . " starts at 0 <br>";
    while ($x < 10) {
        $x++;
        echo '$x' . " + 1 is $x<br>";
    }
    ?>
</p>
<p>Note: we don't see 0 printed out because we output the value after the increment occurs</p>
<br>
<p>We have the do while loop, which will always run the first iteration and only continue if the condition is still true:</p>
<p>$x will be set to 0 and will loop as long as $x is greater than zero.</p>
<p>
<pre><code class="language-php">
$x = 0;
do {
    echo '$x' . " is $x<br>";
} while ($x > 0);
</code></pre>
</p>
<p>Output: <br>
    <?php
    $x = 0;
    do {
        echo '$x' . " is $x<br>";
    } while ($x > 0);
    ?>
</p>
<p>Notice we didn't loop further here since the condition was immediately false? Let's see another example:</p>
<p>
    $x will be set to 10 and will loop as long as $x is greater than zero.
</p>
<p>
<pre><code class="language-php">
$x = 10;
do {
    echo '$x' . " is $x";
    $x--;
} while ($x > 0);
</code></pre>
</p>
<p>Output: <br>
    <?php
    $x = 10;
    do {
        echo '$x' . " is $x<br>";
        $x--;
    } while ($x > 0);
    ?>
</p>
<br>
<p>We have the for loop, which loops a set number of iterations:</p>
<p>Note: first section is the assignment of the iterator, second section is the condition, and third is the iterator increment.</p>
<p>
<pre><code class="language-php">
for ($i = 0; $i < 10; $i++) {
    echo '$i' . " + 1 is $i";
}
</code></pre>
</p>
<p>Output: <br>
    <?php
    for ($i = 0; $i < 10; $i++) {
        echo '$i' . " + 1 is $i<br>";
    }
    ?>
</p>
<p>Note: we see the output of 0 but not 10 because the increment occurs at the end of the iteration</p>
<br>
<p>Lastly, we have two examples of probably the most important iterator of PHP, the foreach loop. We'll be using this one a lot:</p>
<p>Foreach iterates over a collection, keep this in mind. We'll be using this a lot once we get working with the database.</p>
<p>
<pre><code class="language-php">
$myArr = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
foreach ($myArr as $item) {
    echo '$item is ' . $item;
}
</code></pre>
</p>
<p>Output: <br>
    <?php
    $myArr = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    echo "<br>This is our array contents <pre>" . var_export($myArr, true) . "</pre><br>";
    foreach ($myArr as $item) {
        echo '$item is ' . $item . "<br>";
    }
    ?>
</p>
<p>Now that may not have been so helpful, we have no idea what iteration each output was for unless we count it. Should we add another variable and increment it to track the iteration?</p>
<p>No need, we have another way to use the foreach loop that'll separate the key and the value:</p>
<p>
<pre><code class="language-php">
foreach ($myArr as $key => $value) { //notice the $key points to the value via =>
    echo "$key is $value";
}
</code></pre>
</p>
<p>Output: <br>
    <?php
    foreach ($myArr as $key => $value) { //notice the $key points to the value via =>
        echo ($key) . " is $value<br>";
    }
    ?>
</p>
<p>Now we can see the key (in this case the index) and what value it holds. Let's take it a step further and make it more human friendly:</p>
<p>
<pre><code class="language-php">
foreach ($myArr as $key => $value) { //notice the $key points to the value via =>
    echo ($key + 1) . " is $value";
}
</code></pre>
</p>
<p>Output: <br>
    <?php
    foreach ($myArr as $key => $value) { //notice the $key points to the value via =>
        echo ($key + 1) . " is $value<br>";
    }
    ?>
</p>
<p>There we go, we're more familiar with our lists starting at 1, however majority of programming indexes start at 0.</p>
<p>Now let's take a look at the javascript equivalent. It's all include in the same file, all you need to do is open up dev tools on your browser.</p>
<p>For Chrome that's typically F12, otherwise usually you can right click the screen and choose 'inspect' from the context menu.</p>
<p>Then make sure you have the 'console' tab visible as that's where our output will be for now.</p>
<script>
    //https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Loops_and_iteration
    //our regular for loop
    console.log("Javascript also has a while loop. Again, it iterates as long as the condition is true.")
    let x = 0;
    while (x < 10) {
        x++;
        console.log("x + 1 is", x);
    }
    console.log("And a do while. Again, it runs at least once, and continues as long as the condition is true.");
    do {
        x--;
        console.log("x - 1 is", x);
    }
    while (x > 0);
    console.log("Here's the for loop again that iterates a specific number of times.")
    for (let i = 0; i < 10; i++) {
        console.log("Iteration", i);
    }
    console.log("Now, javascript doesn't have a keyword foreach, but the for loop acts as a for each via the 'in' or 'of' operators");
    //taken from the referenced site:
    const arr = [3, 5, 7];
    arr.foo = 'hello';
    console.log(arr);
    //loop over properties
    console.log("'in' iterates over properties");
    for (let i in arr) {
        console.log(i); // logs "0", "1", "2", "foo"
        console.log("arr", arr[i]);
    }
    console.log("_____________");
    console.log("'of' iterates only over iterable parts (i.e., arrays) note the difference of output of the array portion of this object.")
    //loop over iterable objects (i.e., arrays)
    for (let i of arr) {
        console.log(i); // logs 3, 5, 7
        console.log("arr using i as a/an key/index", arr[i], "this is undefined because we already have the value. If this was a key we'd have output, but this is incorrect usage");
    }
</script>
<?php //ignore this, it's for effects on the code
require(__DIR__ . "/../../partials/prism.php");
?>