<?php
$host = "localhost";
$username = "root";
$password = "root";
$database = "db_barang";

// pakai mysqli_connect tapi simpan di $koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// set charset
mysqli_set_charset($koneksi, "utf8");
?>



