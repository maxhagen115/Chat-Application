<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $user_id = $_SESSION['unique_id'];

    mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$user_id}");

    mysqli_query($conn, "
        UPDATE users 
        SET status = 'Afwezig' 
        WHERE unique_id != {$user_id} 
          AND status = 'Actief' 
          AND last_online < (NOW() - INTERVAL 10 SECOND)
    ");
}