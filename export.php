<?php
include 'conn.php';

$sd = "";
$ed = "";
$pm = "1";
function getDataFromDatabase($conn, $startDate, $endDate, $pm)
{
    global $sd, $ed;
    $sd = $startDate;
    $ed = $endDate;
    $sql = "SELECT * FROM pm$pm WHERE time BETWEEN '$startDate' AND '$endDate' ORDER BY time";
    $result = $conn->query($sql);
    return $result;
}

// Ambil data awal (tanpa filter tanggal)
$today = date('Y-m-d');

// Set tanggal awal dan akhir pada formulir input
$startDefault = $today;
$endDefault = $today;

// Jika formulir telah disubmit, ambil data berdasarkan tanggal yang dipilih

if (isset($_POST['submit'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $pm = $_POST['pm'];
    $data = getDataFromDatabase($conn, $startDate, $endDate, $pm);
} else {
    $data = getDataFromDatabase($conn, $today, $today, $pm);
}
// 
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
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

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
    header("Content-Disposition: attachment; filename=pm$pm $sd $ed.xls");
    ?>

    <center>
        <h2>Tabel History Data Power Meter
            <?php echo ($pm) ?>
        </h2>
        <h3>Periode
            <?php echo ($sd . " sampai " . $ed) ?>
        </h3>
    </center>

    <table border="1">
        <tr>
            <th>No</th>
            <th>Time</th>
            <th>Volt R-N</th>
            <th>Volt S-N</th>
            <th>Volt T-N</th>
            <th>Current R</th>
            <th>Current S</th>
            <th>Current T</th>
            <th>Watt R</th>
            <th>Watt S</th>
            <th>Watt T</th>
            <th>Frequency</th>
            <th>Power Factor</th>
            <th>Power in Load</th>
            <th>Power out to Load</th>
        </tr>
        <?php
        $no = 1;
        while ($d = mysqli_fetch_array($data)) {
            ?>
            <tr>
                <td>
                    <?php echo $no++; ?>
                </td>
                <td>
                    <?php echo $d['time']; ?>
                </td>
                <td>
                    <?php echo $d['v_r']; ?>
                </td>
                <td>
                    <?php echo $d['v_s']; ?>
                </td>
                <td>
                    <?php echo $d['v_t']; ?>
                </td>
                <td>
                    <?php echo $d['i_r']; ?>
                </td>
                <td>
                    <?php echo $d['i_s']; ?>
                </td>
                <td>
                    <?php echo $d['i_t']; ?>
                </td>
                <td>
                    <?php echo $d['w_r']; ?>
                </td>
                <td>
                    <?php echo $d['w_s']; ?>
                </td>
                <td>
                    <?php echo $d['w_t']; ?>
                </td>

                <td>
                    <?php echo $d['freq']; ?>
                </td>
                <td>
                    <?php echo $d['pf']; ?>
                </td>
                <td>
                    <?php echo $d['p_in']; ?>
                </td>
                <td>
                    <?php echo $d['p_out']; ?>
                </td>
            </tr>

            <?php
        }
        ?>
    </table>
</body>

</html>