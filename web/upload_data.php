<?php
require_once ('creds.php');
require_once ('auth_app.php');
require_once ('parse_functions.php');

// Create an array of all the existing fields in the database
$result = $db->query("SHOW COLUMNS FROM $db_table") or die($db->error);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dbfields[]=($row['Field']);
    }
}

// Get the torque key->val mappings
$js = CSVtoJSON("./data/torque_keys.csv");
$jsarr = json_decode($js, TRUE);

// Iterate over all the k* _GET arguments to check that a field exists
if (sizeof($_GET) > 0) {
    $keys = array();
    $values = array();
    foreach ($_GET as $key => $value) {
        // Keep columns starting with k
        if (preg_match("/^k/", $key)) {
            $keys[] = $key;
            $values[] = $value;
            $submitval = 1;
        }
        else if (in_array($key, array("v", "eml", "time", "id", "session"))) {
            $keys[] = $key;
            $values[] = "'".$value."'";
            $submitval = 1;
        }
        // Skip columns matching userUnit*, defaultUnit*, and profile*
        else if (preg_match("/^userUnit/", $key) or preg_match("/^defaultUnit/", $key) or (preg_match("/^profile/", $key) and (!preg_match("/^profileName/", $key)))) {
            $submitval = 0;
        }
        else {
            $submitval = 0;
        }
        // NOTE: Use the following "else" statement instead of the one above
        //       if you want to keep anything else.
        //else {
        //    $keys[] = $key;
        //    $values[] = "'".$value."'";
        //    $submitval = 1;
        //}
        // If the field doesn't already exist, add it to the database
        if (!in_array($key, $dbfields) and $submitval == 1) {
            $comment = '';
            if (in_array($key, $jsarr)) {
                $comment = $jsarr[$key];
            }
            $comment = $db->real_escape_string($comment);
            $sqlalter = "ALTER TABLE $db_table ADD $key FLOAT NOT NULL DEFAULT '0' COMMENT '$comment'";
            $db->query($sqlalter) or die($db->error);
        }
    }
    if ((sizeof($keys) === sizeof($values)) && sizeof($keys) > 0) {
        // Now insert the data for all the fields
        $sql = "INSERT INTO $db_table (".implode(",", $keys).") VALUES (".implode(",", $values).")";
        $db->query($sql) or die($db->error);
    }
}

// Return the response required by Torque
echo "OK!";

?>
