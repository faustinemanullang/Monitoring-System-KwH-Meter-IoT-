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

    $data = [];  // Initialize an empty array to store the data

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['time'] = date('Y-m-d', strtotime($row['date'] . ' +7 hours'));
            $row['lwbp'] = $row['lwbp'] !== null ? number_format($row['lwbp'], 2) : 0;
            $row['wbp'] = $row['wbp'] !== null ? number_format($row['wbp'], 2) : 0;

            $data[] = $row;
        }
    }

    return $data;  // Return the data array
}


if (isset($_POST['submit'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $data1 = getDataFromDatabase($conn, $startDate, $endDate, 1);
    $data2 = getDataFromDatabase($conn, $startDate, $endDate, 2);

    // // Combine data based on date
    // $combinedData = [];

    // // Use data from $data1 as base
    // foreach ($data1 as $row1) {
    //     $date = $row1['date'];
    //     $combinedData[$date] = [
    //         'date' => $date,
    //         'lwbp1' => $row1['lwbp'],
    //         'wbp1' => $row1['wbp'],
    //     ];

    //     // If date exists in $data2, add data to the combined array
    //     foreach ($data2 as $row2) {
    //         if ($row2['date'] === $date) {
    //             $combinedData[$date]['lwbp2'] = $row2['lwbp'];
    //             $combinedData[$date]['wbp2'] = $row2['wbp'];
    //         }
    //     }
    // }
} else {
    $current_time = time();
    $one_hour_ago = $current_time - (3600 * 24 * 7);
    $todayh = date('Y-m-d', $current_time);
    $today = date('Y-m-d', $one_hour_ago);
    $data1 = getDataFromDatabase($conn, $today, $todayh, 1);
    $data2 = getDataFromDatabase($conn, $today, $todayh, 2);

    // // Combine data based on date
    // $combinedData = [];

    // // Use data from $data1 as base
    // foreach ($data1 as $row1) {
    //     $date = $row1['date'];
    //     $combinedData[$date] = [
    //         'date' => $date,
    //         'lwbp1' => $row1['lwbp'],
    //         'wbp1' => $row1['wbp'],
    //     ];

    //     // If date exists in $data2, add data to the combined array
    //     foreach ($data2 as $row2) {
    //         if ($row2['date'] === $date) {
    //             $combinedData[$date]['lwbp2'] = $row2['lwbp'];
    //             $combinedData[$date]['wbp2'] = $row2['wbp'];
    //         }
    //     }
    // }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/fa/css/all.min.css" />
    <link rel="stylesheet" href="css/bs/bootstrap.min.css" />
    <script src="script/ap/apexcharts.js"></script>
    <title>POWER METER MONITORING</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: whitesmoke;
            color: midnightblue;
        }

        .wrapper {
            display: flex;
            height: 100vh;
        }

        #sidebar {
            background-color: white;
            color: midnightblue;
            padding: 4px;
            width: 250px;
            transition: all 0.3s;
        }

        #sidebar.active {
            width: 0;
            padding: 0px;
            overflow-x: hidden;
        }

        #content {
            flex: 1;
            padding: 8px;
        }

        #judul {
            font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
                "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
            font-size: 16px;
            font-weight: 600;
        }

        .card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn i {
            margin-right: 16px;
            /* Sesuaikan spasi sesuai kebutuhan */
        }

        .btn {
            text-align: left;
        }

        .cardBox {
            display: flex;
            justify-content: space-around;
        }

        .cardBox .card {
            width: 95%;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th,
        td {
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h5 style="text-align: center; margin-bottom: 30px; margin-top: 20px">
                    POWER METER MONITORING
                </h5>
            </div>
            <!-- Add your sidebar content here -->
            <ul class="list-unstyled components">
                <li>
                    <a href="index.html"><button type="button" class="btn btn-link btn-block">
                            <span><i class="fas fa-home"></i></span> HOME
                        </button>
                    </a>
                </li>
                <li style="border-radius: 10px; color: darkgray; margin-top: 10px">
                    <button type="button" class="btn btn-primary btn-block">
                        POWER CONSUMPTION
                    </button>
                </li>
                <li>
                    <img src="assets/pm.png" alt="logoo" width="150px" height="150px" style="
                                margin-left: 40px;
                                margin-top: 50px;
                                margin-bottom: 30px;
                            " />
                </li>
                <li>
                </li>
                <li style="margin-top: 32px">
                    <div class="card" style="width: 95%; text-align: center; color: black">
                        <div style="margin-bottom: 16px; font-size: 1.2rem">
                            FILTER DATA GRAFIK
                        </div>
                        <form method="post">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="<?php echo ($sd) ?>" required /><br />
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="<?php echo ($ed) ?>" required /><br />
                            <button type="submit" name="submit" class="btn btn-success btn-block"
                                style="text-align: center; margin-top: 20px">
                                <i class="fas fa-filter"></i>Filter
                            </button>
                        </form>
                    </div>
                </li>

            </ul>
        </nav>

        <!-- Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid mb-2">
                    <!-- Hamburger Button -->
                    <button type="button" id="sidebarCollapse" class="btn">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div id="judul" style="text-align:center">
                        <div>DATA LOG POWER CONSUMPTION</div>
                        <div>Periode
                            <?php echo ($sd . " sampai " . $ed) ?>
                        </div>
                    </div>
                    <div class="ml-2">
                        <img src="assets/logo.jpg" alt="Logo" width="50" height="50" />
                    </div>
                </div>
            </nav>
            <div class="cardBox">

                <div class="card" id="chart-pm1"></div>
            </div>
            <div class="cardBox">
                <div class="card" id="chart-pm2"></div>
            </div>




            <div class="cardBox">
                <form method="post" action="exportp.php">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?php echo ($sd) ?>" />
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?php echo ($ed) ?>">
                    <input type="hidden" name="pm" value="1">
                    <button type="submit" name="submit" class="btn btn-success  btn-lg"
                        style="text-align: center; margin: 20px">
                        <i class="fas fa-download" style="margin-right:30;"></i>DOWNLOAD REPORT
                    </button>
                </form>
            </div>

        </div>
    </div>
    <script src="script/jquery-3.2.1.slim.min.js"></script>
    <script src="script/popper.min.js"></script>
    <script src="script/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#sidebarCollapse").on("click", function () {
                $("#sidebar").toggleClass("active");
            });
        });

        // Assuming you have the data for PM1 and PM2 stored in $data1 and $data2 respectively

        // Function to create ApexCharts column chart
        function createColumnChart(containerId, chartData, pm) {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                },
                series: [{
                    name: 'LWBP',
                    data: chartData.map(entry => parseFloat(entry.lwbp)),
                }, {
                    name: 'WBP',
                    data: chartData.map(entry => parseFloat(entry.wbp)),
                }],
                xaxis: {
                    categories: chartData.map(entry => entry.date),
                },
                title: {
                    text: `Grafik Power Meter ${pm}`,
                    align: 'center',
                },
                yaxis: {
                    title: {
                        text: 'Energy Consumption (KwH)',
                    },
                },
            };

            var chart = new ApexCharts(document.getElementById(containerId), options);
            chart.render();
        }

        // Call the function to create column chart for PM1
        createColumnChart('chart-pm1', <?php echo json_encode($data1); ?>, 1);

        // Call the function to create column chart for PM2
        createColumnChart('chart-pm2', <?php echo json_encode($data2); ?>, 2);
    </script>
</body>

</html>
<?php
// Tutup koneksi database setelah selesai
$conn->close();
?>