<?php
include 'conn.php';
$data1;
$data2;
$sd = "";
$ed = "";
function getDataFromDatabase($conn, $startDate, $endDate, $pm)
{
    global $sd, $ed;
    $sd = $startDate;
    $ed = $endDate;

    $sql = "SELECT
ttime,
DATE(ttime) AS date,
MAX(
CASE WHEN HOUR(ttime) BETWEEN 0 AND 19 AND p_in > -1 THEN p_in END
) - MIN(
CASE WHEN HOUR(ttime) BETWEEN 0 AND 19 AND p_in > -1 THEN p_in END
) AS lwbp,
MAX(
CASE WHEN HOUR(ttime) BETWEEN 20 AND 23 AND p_in > -1 THEN p_in END
) - MIN(
CASE WHEN HOUR(ttime) BETWEEN 20 AND 23 AND p_in > -1 THEN p_in END
) AS wbp
FROM (
SELECT
DATE_ADD(TIME, INTERVAL 2 HOUR) AS ttime,
p_in
FROM pm$pm
) AS subquery
WHERE DATE(ttime) BETWEEN '$startDate' AND '$endDate'
GROUP BY DATE(ttime)";

    $result = $conn->query($sql);

    $data = []; // Initialize an empty array to store the data

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['time'] = date('Y-m-d', strtotime($row['date'] . ' +7 hours'));
            $row['lwbp'] = $row['lwbp'] !== null ? number_format($row['lwbp'], 2) : "0.00";
            $row['wbp'] = $row['wbp'] !== null ? number_format($row['wbp'], 2) : "0.00";
            $data[] = $row;
        }
    }

    return $data; // Return the data array
}


if (isset($_POST['submit'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $data1 = getDataFromDatabase($conn, $startDate, $endDate, 1);
    $data2 = getDataFromDatabase($conn, $startDate, $endDate, 2);

    // Combine data based on date
    $combinedData = [];

    // Use data from $data1 as base
    foreach ($data1 as $row1) {
        $date = $row1['date'];
        $combinedData[$date] = [
            'date' => $date,
            'lwbp1' => $row1['lwbp'],
            'wbp1' => $row1['wbp'],
        ];

        // If date exists in $data2, add data to the combined array
        foreach ($data2 as $row2) {
            if ($row2['date'] === $date) {
                $combinedData[$date]['lwbp2'] = $row2['lwbp'];
                $combinedData[$date]['wbp2'] = $row2['wbp'];
            }
        }
    }
} else {
    $current_time = time();
    $one_hour_ago = $current_time - (3600 * 24 * 7);
    $todayh = date('Y-m-d', $current_time);
    $today = date('Y-m-d', $one_hour_ago);
    $data1 = getDataFromDatabase($conn, $today, $todayh, 1);
    $data2 = getDataFromDatabase($conn, $today, $todayh, 2);

    // Combine data based on date
    $combinedData = [];

    // Use data from $data1 as base
    foreach ($data1 as $row1) {
        $date = $row1['date'];
        $combinedData[$date] = [
            'date' => $date,
            'lwbp1' => $row1['lwbp'],
            'wbp1' => $row1['wbp'],
        ];

        // If date exists in $data2, add data to the combined array
        foreach ($data2 as $row2) {
            if ($row2['date'] === $date) {
                $combinedData[$date]['lwbp2'] = $row2['lwbp'];
                $combinedData[$date]['wbp2'] = $row2['wbp'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Export Data</title>
</head>

<body>
    <style type="text/css">
        body {
            font-family: sans-serif;
            text-align: center;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: center;
        }

        table th,
        table td {
            border: 2px solid #3c3c3c;
            padding: 8px 8px;

        }


        a {
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=power $sd $ed.xls");
    ?>


    <div style="text-align:center;">
        <h2>Tabel History Daya Power Meter</h2>
        <h3>Periode
            <?php echo ($sd . " sampai " . $ed) ?>
        </h3>
        <?php
        // function generateTable($data, $pm)
        // {
        //     echo "<h2>Power Meter $pm</h2>";
        //     echo "<table>";
        //     echo "<tr>
        //     <th>Tanggal</th>
        //     <th>LWBP (KwH)</th>
        //     <th>WBP (KwH)</th>
        // </tr>";
        
        //     foreach ($data as $row) {
        //         echo "<tr>";
        //         echo "<td>{$row['date']}</td>";
        //         echo "<td>{$row['lwbp']}</td>";
        //         echo "<td>{$row['wbp']}</td>";
        //         echo "</tr>";
        //     }
        
        //     echo "</table>";
        // }
        
        // // Display tables for both PM1 and PM2
        // generateTable($data1, 1);
        // generateTable($data2, 2);
        ?>

        <?php
        // Function to generate HTML table from combined data
        function generateCombinedTable($combinedData)
        {
            echo "
            <table> <tr>
        <th rowspan=\"2\">Date</th>
        <th colspan=\"2\">Power Meter 1</th>
        <th colspan=\"2\">Power Meter 2</th>
    </tr>
    <tr>
        <th>LWBP (KwH)</th>
        <th>WBP (KwH)</th>
        <th>LWBP (KwH)</th>
        <th>WBP (KwH)</th>
    </tr>";

            foreach ($combinedData as $row) {
                echo "<tr>";
                echo "<td>{$row['date']}</td>";
                echo "<td>{$row['lwbp1']}</td>";
                echo "<td>{$row['wbp1']}</td>";
                echo "<td>{$row['lwbp2']}</td>";
                echo "<td>{$row['wbp2']}</td>";
                echo "</tr>";
            }

            echo "</table>";
        }

        // Display the combined table
        generateCombinedTable($combinedData);
        ?>
    </div>
</body>

</html>