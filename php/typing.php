<?php
session_start();
include_once "config.php";

$typing_to = mysqli_real_escape_string($conn, $_POST['typing_to']);
$unique_id = $_SESSION['unique_id'];

// Update typing_to
$sql = mysqli_query($conn, "UPDATE users SET typing_to = '{$typing_to}' WHERE unique_id = {$unique_id}");

mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$unique_id}");
