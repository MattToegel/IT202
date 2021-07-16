<h1>Module 09 Table of Contents</h1>
<p>Per request, this section will show a few ways to handle image upload.</p>
<p>Note: The traditional way will not work for Heroku because Heroku uses a temporary filesystem. As soon as
    the VM restarts, your saved data is gone. But we have a few work arounds as well as the "correct" way to do modern image uploads.
<p>
<ul>
    <li>Work Around 1: Create an images folder under public_html or under your Project folder, preload the folder with images, and reference them via url (like I did for Quarry.php)</li>
    <li>Work around 2: Save the base64 of the image as text in the database (this is probably one of the worst solutions since you typically want to separate data and storage and typically you'll want to manage your DB size and not have it chewed up by images)</li>
    <li>The "Standard" Way that won't work: <a href="standard.php">here</a></li>
    <li>Using a third party image service (flickr, imgur, imgbb, etc): <a href="imgbb.php">here</a></li>
    <li>Recommended: Using a storage service (AWS S3, GCE Storage, Firebase Storage, etc): <a href="firebase.php">here</a></li>

    <li><a href="..">Back</a></li>
</ul>
</ul>