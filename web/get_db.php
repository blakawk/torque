<?php

require_once("./creds.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db->connect_errno) {
    die($db->connect_error);
}
$db->set_charset('utf8mb4');

?>