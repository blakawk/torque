<?php
require_once("./creds.php");

if (isset($_POST["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_POST['deletesession']);
}
elseif (isset($_GET["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_GET['deletesession']);
}

if (isset($deletesession) && !empty($deletesession)) {
    $db->query("DELETE FROM $db_table
                WHERE session=$deletesession;", $con) or die($db->error);
}

?>
