<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $user_id = $_SESSION['unique_id'];
    
    // Skip update for demo users
    if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true) {
        exit;
    }
    
    // Escape and check if numeric
    $user_id_escaped = mysqli_real_escape_string($conn, $user_id);
    $is_numeric = is_numeric($user_id);
    $user_id_sql = $is_numeric ? $user_id : "'{$user_id_escaped}'";

    mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$user_id_sql}");

    mysqli_query($conn, "
        UPDATE users 
        SET status = 'Afwezig' 
        WHERE unique_id != {$user_id_sql} 
          AND status = 'Actief' 
          AND last_online < (NOW() - INTERVAL 10 SECOND)
    ");
}