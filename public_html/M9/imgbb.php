<h1>IMGBB</h1>
<h3><a href="index.php">Back</a></h3>
<p>For this sample I went with IMGBB since it only required an API Key.</p>
<p>Most services require client_id and client_secret and to go through an Oauth process which isn't really beginner friendly. And requires extra requests</p>
<a href="https://api.imgbb.com/">imgbb API reference</a>
<h5>Pre-setup Steps for Local <b>Only</b></h5>
<ul>
    <li>
        <img src="images/enable_curl.png" alt="Enable curl in php.ini if not enabled" /> <br> Enable curl
    </li>
    <li><a href="https://curl.se/docs/caextract.html">Get the latest cacert.pem file</a></li>
    <li>Create a new folder in your php directory (mine is D:\php\cert)</li>
    <li>Put the downloaded cacert.pem file into that directory</li>
    <li><img src="images/cacert_php_ini.png" alt="Set path to cert" /> <br> Set the absolute path to the cacert file</li>
    <li>Any time you edit your ini file, make sure you reload the dev server</li>
</ul>
<form method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
<?php
// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $target_dir = "images/"; //this will go to my images folder under M9 folder
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    if ($uploadOk > 0) {
        //pulling my api key from my secret .env file, you'll need to add your own key to the .env file for local testing
        // and/or the Heroku Config vars just like we did for mysql

        $ini = @parse_ini_file(__DIR__ . "/../../lib/.env");
        if ($ini && isset($ini["IMGBB_KEY"])) {
            //load local .env file
            $api_key = ($ini["IMGBB_KEY"]);
        } else {
            //load from heroku env variables
            $api_key      = (getenv("IMGBB_KEY"));
        }
        $img = curl_file_create($_FILES["fileToUpload"]["tmp_name"], "image/$imageFileType");

        // set post fields
        $post = [
            'key' => $api_key, //The API key.
            'image' => $img, //A binary file, base64 data, or a URL for an image. (up to 32 MB)
            //adding datetime to the name so same named files aren't overwritten; make sure you convert it to filename safe output
            'name'   => $_FILES["fileToUpload"]["tmp_name"] . date('Y-m-d H_i_s'), //The name of the file, this is automatically detected if uploading a file with a POST and multipart / form-data
            //can also pass expiration, but I globally set mine to delete after 5 mins
        ];
        //using CURL to send the request from php, note this is helpful for classes where you'll be working with APIs from the server side
        $ch = curl_init('https://api.imgbb.com/1/upload?key=$api_key');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            echo "<pre>" . var_export(json_decode($response, true), true) . "</pre>";
            echo "<br> Then you'd extract the desired url(s) from the decoded response and save them in the database";
        }
        // execute!

        // close the connection, release resources used
        curl_close($ch);

        // do anything you want with your response

    }
}
