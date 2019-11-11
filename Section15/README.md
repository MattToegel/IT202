*JQuery Intro*

1. Review the Selectors: 
	1. Simplified: https://www.w3schools.com/jquery/jquery_ref_selectors.asp 
	2. Detailed: https://api.jquery.com/category/selectors/
2. Grab the (Content Delivery Network)CDN Link: (pick one under jQuery 3.x) https://code.jquery.com/
	1. It's preferred that you go with uncompressed for developmenet purposes so you can view the code in how the jQuery developers implement certain things.
3. Add to your code
```javascript
<!--replace url with the chosen one from step 2-->
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
```

4. Replace some vanilla JavaScript functions:
6. Equivalent to ```html <body onload="function()">```
```javascript
$(document).ready(function(){
  $("button").click(function(){
    $("p").slideToggle();
  });
});
```
7. Equivalent of ```html <form onsubmit="function()">```
```javascript
$("form").submit(function(){
  alert("Submitted");
});
```
8. Replace previous ajax method
```javascript
$("button").click(function(){
  $.ajax({url: "demo_test.txt", success: function(result){
    $("#div1").html(result);
  }});
});
```
9. Change CSS: https://www.w3schools.com/jquery/jquery_css.asp
```javascript
$("p").css("background-color");
```
10. Other event bindings/triggers: https://www.w3schools.com/jquery/jquery_ref_events.asp
11. Traversing with jQuery: https://www.w3schools.com/jquery/jquery_ref_traversing.asp
12. Important to note, the data function: https://www.w3schools.com/jquery/misc_data.asp
	1. Scores key/value pairs on elements where you don't need to rely on hidden input fields and you don't need to worry about the data becoming visible on the screen. (This doesn't mean it's hidden).
```javascript
$("#btn1").click(function(){
  $("div").data("greeting", "Hello World");
});
$("#btn2").click(function(){
  alert($("div").data("greeting"));
});
```
