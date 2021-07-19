<h1>Firebase Example</h1>
<h3><a href="index.php">Back</a></h3>
<b>Note: Most Firebase web stuff is done via the client-side since they follow a different architecture. Serveless functions are typically their backend (via node.js)</b>
<ul>
    <li>Create a Firebase project: <a href="https://firebase.google.com/docs/storage/web/start">https://firebase.google.com/docs/storage/web/start
        </a></li>
    <li>Create a reference to a storage bucket: <a href="https://firebase.google.com/docs/storage/web/create-reference">https://firebase.google.com/docs/storage/web/create-reference
        </a></li>
    <li>Upload files: <a href="https://firebase.google.com/docs/storage/web/upload-files">https://firebase.google.com/docs/storage/web/upload-files
        </a></li>
    <li>You'll have to set the proper storage rules via the firebase dashboard. It's a json file giving true/false permissions. Default read/write requires sign-in. <br><img src="images/fb-rules.png" /></li>
    <li>You'll likely want to create some lifecycle policy. For this sample I have files purge after 1 day.<br>Note: Firebase is owned by Google, but some things can only be access on console.cloud.google.com like this setting.<br><img src="images/fb-lifecycle.png" /></li>
</ul>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.7.1/firebase-app.js"></script>
<!-- Include the storage api -->
<script src="https://www.gstatic.com/firebasejs/8.7.1/firebase-storage.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
    // My web app's Firebase configuration (yours will be different so do NOT use mine below)
    var firebaseConfig = {
        apiKey: "AIzaSyASYRqDseFzDsUbCv_bLICsrqf_nI_Qzaw",
        authDomain: "class-samples.firebaseapp.com",
        projectId: "class-samples",
        storageBucket: "class-samples.appspot.com",
        messagingSenderId: "847766908447",
        appId: "1:847766908447:web:04862c6247c07623f12f2a"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    // Get a reference to the storage service, which is used to create references in your storage bucket
    var storage = firebase.storage();

    function upload(event) {

        let files = event.target.fileToUpload.files;
        if (files.length > 0) {
            let file = files[0];
            console.log(file);
            storage.ref().child("images/" + file.name).put(file).then(res => {
                console.log(res);
                res.ref.getDownloadURL().then((downloadURL) => {
                    //this is the url you'd save in the database
                    console.log('File available at', downloadURL);
                    document.getElementById("dest").src = downloadURL;
                });
                //alert(JSON.stringify(res));
            }).catch(err => {
                console.log(err);
                //alert(JSON.stringify(err));
            })
        }
    }
</script>
<form method="post" onsubmit="upload(event); return false;" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
<img id="dest" />