<?php
require_once("./creds.php");

$db->select_db("INFORMATION_SCHEMA");

// Create array of column name/comments for chart data selector form
$colqry = $db->query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE
                           FROM COLUMNS WHERE TABLE_SCHEMA='".$db_name."'
                           AND TABLE_NAME='".$db_table."'") or die($db->error);

// Select the column name and comment for data that can be plotted.
while ($x = $colqry->fetch_array()) {
    if ((substr($x[0], 0, 1) == "k") && ($x[2] == "float")) {
        $coldata[] = array("colname"=>$x[0], "colcomment"=>$x[1]);
    }
}

$numcols = strval(count($coldata)+1);

$colqry->free_result();


//TODO: Do this once in a dedicated file
if (isset($_POST["id"])) {
    $session_id = preg_replace('/\D/', '', $_POST['id']);
}
elseif (isset($_GET["id"])) {
    $session_id = preg_replace('/\D/', '', $_GET['id']);
}

$db->select_db($db_name) or die($db->error);

// If we have a certain session, check which colums contain no information at all
$coldataempty = array();
if (isset($session_id)) {
    //Count distinct values for each known column
    //TODO: Unroll loop into single query
    foreach ($coldata as $col)
    {
        $colname = $col["colname"];

        // Count number of different values for this specific field
        $colqry = $db->query("SELECT count(DISTINCT $colname)<2 as $colname
                               FROM $db_table
                               WHERE session=$session_id") or die($db->error);
        $colresult = $colqry->fetch_assoc();
        $coldataempty[$colname] = $colresult[$colname];
        $colqry->free_result();
    }

    //print_r($coldataempty);
}

?>
