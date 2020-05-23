<?php
$cleardb_url      = parse_url(getenv("CLEARDB_DATABASE_URL"));
$dbhost   = $cleardb_url["host"];
$dbuser = $cleardb_url["user"];
$dbpass = $cleardb_url["pass"];
$dbdatabase       = substr($cleardb_url["path"],1);
?>