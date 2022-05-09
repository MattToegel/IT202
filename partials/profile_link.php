<?php
if (!isset($user_id)) {
    $user_id = 0;
}
if (!isset($username)) {
    $username = "";
}
?>
<a href="<?php echo get_url("profile.php?id=");
            se($user_id); ?>"><?php se($username); ?></a>