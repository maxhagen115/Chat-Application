<?php
session_start();
include_once "config.php";

// Skip for demo users
if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true) {
    exit;
}

$typing_to = mysqli_real_escape_string($conn, $_POST['typing_to']);
$unique_id = $_SESSION['unique_id'];

// Escape and check if numeric
$unique_id_escaped = mysqli_real_escape_string($conn, $unique_id);
$is_numeric = is_numeric($unique_id);
$unique_id_sql = $is_numeric ? $unique_id : "'{$unique_id_escaped}'";

$sql = mysqli_query($conn, "UPDATE users SET typing_to = '{$typing_to}' WHERE unique_id = {$unique_id_sql}");

mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$unique_id_sql}");
