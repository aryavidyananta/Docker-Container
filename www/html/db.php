<?php
$host = "mysql"; // Host database
$user = "myuser"; // Username database Anda
$password = "password"; // Password database Anda
$database = "mydb"; // Nama database Anda

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
