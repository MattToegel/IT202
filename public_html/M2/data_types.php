<h1>PHP and JS Data Types Comparison</h1>
<h3><a href="index.php">Back</a></h3>
<?php
//PHP requirements:
//1) file must end with the .php extension
//2) php code must be between an opening and closing php tag: <?php ? > (note: the extra space is there so the color coding doesn't break)
?>
<p>Here we'll see how we can reassign different values to $x and see how the data determines the data type</p>
<p>Basically, the contents determines the type of 'box' the variable will turn into.</p>
<p>We'll be using var_dump() to see what data type we get.</p>
<p>
    <?php
    $x = 1;
    echo '1) $x assigned 1 is a/an ';
    var_dump($x);
    ?>
</p>
<br>
<p>
    <?php
    $x = 1.0;
    echo '2) $x assigned 1.0 a/an ';
    var_dump($x);
    ?>
</p>
<p>Note how var_dump is truncating the decimal here</p>
<br>
<p>
    <?php
    $x = "Hi";
    echo '3) $x assigned "Hi" a/an ';
    var_dump($x);
    ?>
</p>
<br>
<p>
    <?php
    $x = [];
    echo '4) $x assigned [] a/an ';
    var_dump($x);
    ?>
</p>
<br>
<p>
    <?php
    $x = array();
    echo '5) $x assigned array a/an ';
    var_dump($x);
    ?>
</p>
<p>
    <br>
    <?php
    $x = false;
echo '6) $x assigned false a/an ';
    var_dump($x);
    ?>
</p>
<br>
<p>
    <?php
    $x = true;
echo '7) $x assigned true a/an ';
    var_dump($x);
    echo "<br><br>";
    ?>
</p>
<br>
<p>
    <?php
    $x = null;
echo '8) $x assigned null a/an ';
    var_dump($x);
    ?>
</p>
<br>
<p>
    <?php
    class Test
    {
    };
    $x = new Test();
    echo '9) $x assigned object Test a/an ';
    var_dump($x);
    ?>
</p>
<br>
<br>
<p>Let's take a look at some different types of arrays we have since we're talking about data types.</p>
<p>Below is an indexed based array. That means it uses numbered slots starting at 0 to hold values.</p>
<p>
    <?php
    $indexed_array = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    echo "<pre>" . var_export($indexed_array, true) . "</pre>";
    ?>
</p>
<br>
<p>Next is an associative array, instead of numbers for the slots it uses strings as keys. Each key points to a value.</p>
<p>
    <?php
    $associative_array = ["Day 1" => "Monday", "Day 2" => "Tuesday", "Day 3" => "Wednesday", "Day 4" => "Thursday", "Day 5" => "Friday"];
    echo "<pre>" . var_export($associative_array, true) . "</pre>";
    ?>
</p>
<br>
<p>Note: if you repeat an index, it overwrites the original value just as if you were setting a variable multiple times over like the previous examples.</p>
<p>Lastly, we can have arrays of arrays a.k.a multidimensional arrays. This will be very common when we ask our database for a list of rows.</p>
<p>
    <?php
    $multi_array = [
        [1, 2, 3, 4, 5],
        ["A", "B", "C", "D"],
        [null, null, null, null]
    ];
    echo "<pre>" . var_export($multi_array, true) . "</pre>";
    ?>
</p>
<p>Now let's take a look at the javascript equivalent. It's all include in the same file, all you need to do is open up dev tools on your browser.</p>
<p>For Chrome that's typically F12, otherwise usually you can right click the screen and choose 'inspect' from the context menu.</p>
<p>Then make sure you have the 'console' tab visible as that's where our output will be for now.</p>



<script>
    //JavaScript requirements:
    //can be embedded in a .html/.php page like this one
    //or can be an external .js file
    //if the first option, code must be between script tags: <script>< /script> (note: the extra space is there so the color coding doesn't break)
    //if the second option, code needs to be inlcuded via: <script src="/path/to/file.js"></ script> and won't contain JavaScript in between the tags
    console.log("For JavaScript we have the typeof operator. Now, we need to be careful as it may not return what we expect.")
    var myVar = 1;
    console.log("myVar set to 1 is", typeof myVar);
    myVar = 1.0;
    console.log("myVar set to 1.0 is", typeof myVar);
    myVar = "Hi";
    console.log("myVar set to \"Hi\" is", typeof myVar);
    myVar = [];
    console.log("myVar set to [] is", typeof myVar, "this seems a little off. [] is an array");
    myVar = new Array();
    console.log("myVar set to new Array() is", typeof myVar, "this too is an array");
    myVar = false;
    console.log("myVar set to false is", typeof myVar);
    myVar = true;
    console.log("myVar set to true is", typeof myVar);
    myVar = null;
    console.log("myVar set to null is", typeof myVar, "huh that's weird right?");
    myVar = undefined;
    console.log("myVar set to undefined is", typeof myVar);
    myVar = {};
    console.log("myVar set to {} is", typeof myVar);
    myVar = function() {};
    console.log("myVar set to function(){} is", typeof myVar);
    console.log("Check https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/typeof for more details");

    console.log("Regarding arrays in javascript, we just have indexed arrays.");
    myVar = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    console.log("Indexed Array", myVar);
    console.log("We can do something similar to an associative array, but in realitiy it's an object in the javascript world");
    myVar = [];
    myVar["Day 1"] = "Monday";
    myVar["Day 2"] = "Tuesday";
    myVar["Day 3"] = "Wednesday";
    myVar["Day 4"] = "Thursday";
    myVar["Day 5"] = "Friday";
    console.log("My associative array...object", myVar);
    console.log("Lastly, here's our multidimensional array sample");
    myVar = [
        [1, 2, 3, 4, 5],
        ["A", "B", "C", "D"],
        [null, undefined, null, undefined]
    ];
    console.log("My multidimensional array", myVar);
</script>
<?php //ignore this, it's for effects on the code
require(__DIR__ . "/../../partials/prism.php");
?>