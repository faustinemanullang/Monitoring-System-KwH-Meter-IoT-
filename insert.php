<?php

// Replace these with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pm";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
$sqlcfg = "SELECT * FROM cfg WHERE id=1";
$result = $conn->query($sqlcfg);
$row = $result->fetch_assoc();
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Decode the JSON data from the POST request
    $jsonData = json_decode(file_get_contents("php://input"), true);
    // var_dump($jsonData);
    // Check if JSON decoding was successful
    if ($jsonData !== null) {
        $now = time();
        if ($now % $row["setInterval"] == 0) {
            // Extracting data for insertion
            $id = null; // Assuming id is auto-incremented
            $time = null; // Current date and time
            $v_r = $jsonData[0][0][0];
            $v_s = $jsonData[0][0][1];
            $v_t = $jsonData[0][0][2];
            $i_r = $jsonData[0][1][0];
            $i_s = $jsonData[0][1][1];
            $i_t = $jsonData[0][1][2];
            $w_r = $jsonData[0][2][0];
            $w_s = $jsonData[0][2][1];
            $w_t = $jsonData[0][2][2];
            $freq = $jsonData[0][3];
            $pf = $jsonData[0][4];
            $p_in = $jsonData[0][5][0];

            // SQL query to insert data
            $sql = "INSERT INTO pm1 (id, time, v_r, v_s, v_t, i_r, i_s, i_t, w_r, w_s, w_t, freq, pf, p_in) VALUES (null, current_timestamp(), $v_r, $v_s, $v_t, $i_r, $i_s, $i_t, $w_r, $w_s, $w_t, $freq, $pf, $p_in)";

            // Execute the query
            if ($conn->query($sql) === TRUE) {

                $v_r = $jsonData[1][0][0];
                $v_s = $jsonData[1][0][1];
                $v_t = $jsonData[1][0][2];
                $i_r = $jsonData[1][1][0];
                $i_s = $jsonData[1][1][1];
                $i_t = $jsonData[1][1][2];
                $w_r = $jsonData[1][2][0];
                $w_s = $jsonData[1][2][1];
                $w_t = $jsonData[1][2][2];
                $freq = $jsonData[1][3];
                $pf = $jsonData[1][4];
                $p_in = $jsonData[1][5][0];

                // SQL query to insert data
                $sql = "INSERT INTO pm2 (id, time, v_r, v_s, v_t, i_r, i_s, i_t, w_r, w_s, w_t, freq, pf, p_in) VALUES (null, current_timestamp(), $v_r, $v_s, $v_t, $i_r, $i_s, $i_t, $w_r, $w_s, $w_t, $freq, $pf, $p_in)";

                // Execute the query
                if ($conn->query($sql) === TRUE) {
                } else {
                    echo ("error");
                }
            }
        }

        echo ("cfg:" . $row['setInterval']);
    } else {
        echo ("error");
    }
}

// Close the connection
$conn->close();

?>