<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $user_id = $_SESSION['unique_id'];

    // 1. Update current user's status
    mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$user_id}");

    // 2. Set others to 'Afwezig' if inactive for more than 1 minute
    mysqli_query($conn, "
        UPDATE users 
        SET status = 'Afwezig' 
        WHERE unique_id != {$user_id} 
          AND status = 'Actief' 
          AND last_online < (NOW() - INTERVAL 10 SECOND)
    ");
}