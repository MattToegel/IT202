<h1>Floating Point Gotchas</h1>
<h3><a href="index.php">Back</a></h3>
<p>You must be careful when using floating point numbers or decimals with programming languages.</p>
<p>What may make sense to us, doesn't always work out on a computer. Floats have a certain precision, but due to rounding errors we often run into issues like so:</p>
<p>Given the following code:
<pre><code class="language-php">
$a = 0;
$b = 1.0;
for ($i = 0; $i < 10; $i++) {
    $a += 0.1;
}
echo (($a === $b) ? "yes" : "no");
</code></pre>
</p>
<?php
$a = 0;
$b = 1.0;
for ($i = 0; $i < 10; $i++) {
    $a += 0.1;
    //echo "<br>" . var_export($a, true) . "<br>";
}
?>
<details>
    <summary>Does $a equal $b?</summary>
    <p><?php echo (($a === $b) ? "yes" : "no"); ?></p>
</details>
<details>
    <summary>But why?</summary>
    <p>
        <?php
        echo '$a = ' . var_export($a, true);
        echo "<br>";
        echo '$b = ' . var_export($b, true);
        ?>
    </p>
    <p>Due to floating point precision gargabe data gets added or missed when doing math with floats/decimals and gives inaccurate results.</p>
    <p>Does javascript face the same problem?</p>
    <p>For Chrome that's typically F12, otherwise usually you can right click the screen and choose 'inspect' from the context menu.</p>
    <p>Then make sure you have the 'console' tab visible as that's where our output will be for now.</p>
    <p><b>Note: You're very likely to see this on a quiz/exam as it's crucial to understand.</b></p>
    <p><b>Note 2: just because a certain number of iterations yields a whole number (as you may expect) doesn't mean it'll always be the same value across every machine that runs it.</b></p>
</details>
<script>
    let a = 0;
    let b = 1.0;
    for (let i = 0; i < 10; i++) {
        a += 0.1;
    }
    console.log("Does a === b?", (a === b) ? "yes" : "no");
    console.log(a, b, "it's the same reason as before");
</script>
<?php //ignore this, it's for effects on the code
require(__DIR__ . "/../../partials/prism.php");
?>