<?php
// Sambungkan ke database (gantilah dengan informasi koneksi Anda)
$host = "localhost";
$username = "root";
$password = "";
$database = "pm";

$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Jakarta');
// Fungsi untuk mendapatkan data dari database berdasarkan tanggal
?>