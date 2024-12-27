<?php
require 'db.php';

$action = $_GET['action'];
$id = $_GET['id'] ?? null;

if ($action === 'delete' && $id) {
    $conn->query("DELETE FROM produk WHERE id = $id");
    header('Location: dashboard.php');
    exit;
}

// Add/Edit logic goes here...
?>
