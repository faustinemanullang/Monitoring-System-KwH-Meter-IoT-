<?php
include 'conn.php';
$data;
$sd = "";
$ed = "";
function getDataFromDatabase($conn, $startDate, $endDate)
{
	global $sd, $ed;
	$sd = $startDate;
	$ed = $endDate;

	$sql = "SELECT * FROM pm1 WHERE time BETWEEN '$startDate' AND '$endDate' ORDER BY time";
	static $data;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$row['time'] = date('Y-m-d H:i:s', strtotime($row['time'] . ' +7 hours'));

			$data[] = $row;
		}
	}
	return $data;
}

function lastHour($conn)
{
	$current_time = time();
	$one_hour_ago = $current_time - 3600;
	$todayh = date('Y-m-d H:i', $current_time);
	$today = date('Y-m-d H:i', $one_hour_ago);
	$data = getDataFromDatabase($conn, $today, $todayh);
	return $data;
}

// Jika formulir telah disubmit, ambil data berdasarkan tanggal yang dipilih
if (isset($_POST['submit'])) {
	$startDate = $_POST['start_date'];
	$endDate = $_POST['end_date'];
	$data = getDataFromDatabase($conn, $startDate, $endDate);
} else {
	$data = lastHour($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/fa/css/all.min.css" />
	<link rel="stylesheet" href="css/bs/bootstrap.min.css" />
	<link rel="stylesheet" href="css/fp/flatpickr.min.css" />
	<link rel="stylesheet" href="css/fp/material_green.min.css" />
	<script src="script/ap/apexcharts.js"></script>
	<script src="script/fp/flatpickr"></script>
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
			width: 49%;
			text-align: center;
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
						DATA LOG PM 1
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
							<input type="datetime-local" name="start_date" value="<?php echo ($sd) ?>" required /><br />
							<label for="end_date">Tanggal Akhir</label>
							<input type="datetime-local" name="end_date" value="<?php echo ($ed) ?>" required /><br />
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
						<div>DATA LOG POWER METER 1</div>
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
				<div class="card" id="grafikv"></div>
				<div class="card" id="grafiki"></div>
			</div>
			<div class="cardBox">
				<div class="card" id="grafikw"></div>
				<div class="card" id="grafikp"></div>
			</div>
			<div class="cardBox">
				<div class="card" id="grafikf"></div>
				<div class="card" id="grafikq"></div>
			</div>
			<div class="cardBox">
				<form method="post" action="export.php">
					<label for="start_date">Tanggal Mulai</label>
					<input type="datetime-local" name="start_date" value="<?php echo ($sd) ?>" />
					<label for="end_date">Tanggal Akhir</label>
					<input type="datetime-local" name="end_date" value="<?php echo ($ed) ?>">
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

		flatpickr("#datepicker", {
			enableTime: true,
			dateFormat: "Y-m-d H:i",
			theme: "material_green",
			time_24hr: true
		});
		flatpickr("#dp2", {
			enableTime: true,
			dateFormat: "Y-m-d H:i",
			theme: "material_green",
			time_24hr: true
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", (event) => {
			// Data time series dari PHP
			var seriesData = <?php echo json_encode($data); ?>;
			var f1 = (flt) => {
				return parseFloat(flt.toFixed(1));
			}
			// Mengonversi data ke format yang dapat digunakan oleh ApexCharts
			var datav = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'R-N',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.v_r }))
				}, {
					name: 'S-N',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.v_s }))
				}, {
					name: 'T-N',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.v_t }))
				}],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Voltage (V)'
					}
				},
				title: {
					text: 'Grafik Tegangan'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				},
				toolbar: {
					export: {
						csv: {
							filename: "File Name",
							columnDelimiter: ',',
							headerCategory: 'category',
							headerValue: 'value'
						},
						svg: {
							filename: "svgfile",
						},
						png: {
							filename: undefined,
						}
					}
				}

			};

			// Inisialisasi dan menampilkan time series line chart
			var chartv = new ApexCharts(document.querySelector("#grafikv"), datav);
			chartv.render();

			/// gambar grafik arus
			var datai = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'Phase R',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.i_r }))
				}, {
					name: 'Phase S',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.i_s }))
				}, {
					name: 'Phase T',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.i_t }))
				}],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Ampere (A)'
					}
				},
				title: {
					text: 'Grafik Arus'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				}
			};

			// Inisialisasi dan menampilkan time series line chart
			var charti = new ApexCharts(document.querySelector("#grafiki"), datai);
			charti.render();


			//gambar grafik w

			var dataw = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'Phase R',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.w_r }))
				}, {
					name: 'Phase S',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.w_s }))
				}, {
					name: 'Phase T',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.w_t }))
				}],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Watt (W)'
					}
				},
				title: {
					text: 'Grafik Daya'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				}
			};

			// Inisialisasi dan menampilkan time series line chart
			var chartw = new ApexCharts(document.querySelector("#grafikw"), dataw);
			chartw.render();


			//gambar grafik power
			var datap = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'KwH Meter',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.p_in }))
				},],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Kilowatt/Hour (KwH)'
					}
				},
				title: {
					text: 'Grafik Konsumsi Daya'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				}
			};

			// Inisialisasi dan menampilkan time series line chart
			var chartp = new ApexCharts(document.querySelector("#grafikp"), datap);
			chartp.render();


			//gambar grafik power
			var dataf = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'Frequency',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.freq }))
				},],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Hertz (Hz)'
					}
				},
				title: {
					text: 'Grafik Frekuensi'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				}
			};

			// Inisialisasi dan menampilkan time series line chart
			var chartf = new ApexCharts(document.querySelector("#grafikf"), dataf);
			chartf.render();




			var dataq = {
				chart: {
					type: 'line', // Jenis diagram: line untuk time series line chart
					width: '100%',    // Lebar diagram
					height: 300    // Tinggi diagram
				},
				series: [{
					name: 'Cosphi',
					data: seriesData.map(item => ({ x: new Date(item.time), y: item.pf }))
				},],
				xaxis: {
					type: 'datetime',
					title: {
						text: 'Waktu'
					}
				},
				yaxis: {
					title: {
						text: 'Cosphi'
					}
				},
				title: {
					text: 'Grafik Faktor Daya'
				},
				stroke: {
					width: 3 // Set the line width here (adjust as needed)
				}
			};

			// Inisialisasi dan menampilkan time series line chart
			var chartq = new ApexCharts(document.querySelector("#grafikq"), dataq);
			chartq.render();

		});
	</script>
	<script>
		const upd = () => {
			window.location.href = 'http://localhost/mockup/pm1.php'
			console.log("fire");
		}
		setInterval(upd, 60000);
	</script>
</body>

</html>
<?php
// Tutup koneksi database setelah selesai
$conn->close();
?>