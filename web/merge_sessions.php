<?php
require_once("./creds.php");
require_once("./get_sessions.php");

if (isset($_POST["mergesession"])) {
    $mergesession = preg_replace('/\D/', '', $_POST['mergesession']);
}
elseif (isset($_GET["mergesession"])) {
    $mergesession = preg_replace('/\D/', '', $_GET['mergesession']);
}

if (isset($_POST["mergesessionwith"])) {
    $mergesessionwith = preg_replace('/\D/', '', $_POST['mergesessionwith']);
}
elseif (isset($_GET["mergesessionwith"])) {
    $mergesessionwith = preg_replace('/\D/', '', $_GET['mergesessionwith']);
}

if (isset($mergesession) && !empty($mergesession) && isset($mergesessionwith) && !empty($mergesessionwith) ) {
    //Sessions to be merged must be direct neighbors. 'With' must be younger, thus have a lower array index in $sids
    $idx1 = array_search( $mergesession, $sids);
    $idx2 = array_search( $mergesessionwith, $sids);
    if($idx1 != ($idx2+1)) {
        die("Invalid sessions to be merged. Aborted.");
    }

    $db->query("UPDATE $db_table
                          SET session=$mergesession
                          WHERE session=$mergesessionwith;") or die($db->error);
    //Show merged session
    $session_id = $mergesession;
}

?>
