<?php
// Proses data yang diterima dari JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $value = $_POST['value'];

    // Simpan data ke database (contoh sederhana, sesuaikan dengan struktur dan koneksi database Anda)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pm";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE `cfg` SET `setInterval` = '$value' WHERE `cfg`.`id` = 1; ";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>