<?php
require_once("./creds.php");

$timezone = $_SESSION['time'];

// Get list of unique session IDs
$sessionqry = $db->query("SELECT COUNT(*) as `Session Size`, MIN(time) as `MinTime`, MAX(time) as `MaxTime`, session
                      FROM $db_table
                      GROUP BY session
                      ORDER BY MinTime DESC") or die($db->error);

// Create an array mapping session IDs to date strings
$seshdates = array();
$seshsizes = array();
$sids = array();

while($row = $sessionqry->fetch_assoc()) {
    $session_size = $row["Session Size"];
    $session_duration = $row["MaxTime"] - $row["MinTime"];
    $session_duration_str = gmdate("H:i:s", $session_duration/1000);

    // Drop sessions smaller than 60 data points
    if ($session_size >= 60) {
        $sid = $row["session"];
        $sids[] = preg_replace('/\D/', '', $sid);
        $seshdates[$sid] = date("F d, Y  h:ia", substr($sid, 0, -3));
        $seshsizes[$sid] = " (Length $session_duration_str)";
    }
    else {}
}
$sessionqry->free_result();

?>
