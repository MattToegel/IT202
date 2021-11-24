<h1>Standard File Upload</h1>
<h3><a href="index.php">Back</a></h3>
Example from <a href="https://www.w3schools.com/php/php_file_upload.asp">https://www.w3schools.com/php/php_file_upload.asp</a>
<img src="images/php_file_upload.png" alt="Enable file_upload in php.ini if not enabled" />
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
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
            echo "You may use it in an img tag via " . htmlspecialchars($target_file);
            echo "<img src='" . htmlspecialchars($target_file) . "'/>";
            echo "<br>Normally, you'd save this url in the database for future reference, but Heroku's filesystem isn't permenant so after it restarts you'll get a 404 for the image resource";
            echo "<br>Note: if you run this on local you'll see the file created in the images folder, and if you add/commit/push it'd be included with your changes";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>