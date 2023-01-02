<?php
require("./creds.php");
require("./parse_functions.php");

if (isset($_GET["sid"])) {
    // Get the torque key->val mappings
    $js = CSVtoJSON("./data/torque_keys.csv");
    $jsarr = json_decode($js, TRUE);

    $session_id = $db->real_escape_string($_GET['sid']);
    // Get data for session
    $output = "";
    $sql = $db->query("SELECT * FROM $db_table WHERE session=$session_id ORDER BY time DESC;") or die($db->error);

    if ($_GET["filetype"] == "csv") {
        $columns_total = $sql->field_count;

        // Get The Field Name
        for ($i = 0; $i < $columns_total; $i++) {
            $finfo = $sql->fetch_field_direct($i);
            $heading = $finfo->name;
            if (array_key_exists($heading, $jsarr)) {
                $heading = $jsarr[$heading];
            }
            $output .= ''.$heading.';';
        }
        $output .="\r\n";

        // Get Records from the table
        while ($row = $sql->fetch_array()) {
            for ($i = 0; $i < $columns_total; $i++) {
                $output .=''.str_replace(".", ",", $row["$i"]).';';
            }
            $output .="\r\n";
        }

        $sql->free_result();

        // Download the file
        $csvfilename = "torque_session_".$session_id.".csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$csvfilename);

        echo $output;
        exit;
    }
    else if ($_GET["filetype"] == "json") {
        $rows = array();
        while($r = $sql->fetch_assoc()) {
            $rows[] = $r;
        }
        $jsonrows = json_encode($rows);

        $sql->free_result();

        // Download the file
        $jsonfilename = "torque_session_".$session_id.".json";
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename='.$jsonfilename);

        echo $jsonrows;
        exit;
    }
    else {
        exit;
    }
}
else {
    exit;
}

?>
