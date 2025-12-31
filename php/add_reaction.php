<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $unique_id = $_SESSION['unique_id'];
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $emoji = mysqli_real_escape_string($conn, $_POST['emoji']);

    // Handle demo users - use a special user_id for all demo users
    if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true) {
        // Use a special user_id for demo users (999999)
        // First, ensure this user exists in the database
        $demo_user_id = 999999;
        $check_demo_user = mysqli_query($conn, "SELECT user_id FROM users WHERE user_id = {$demo_user_id}");
        
        if (mysqli_num_rows($check_demo_user) == 0) {
            // Create a placeholder demo user if it doesn't exist
            mysqli_query($conn, "INSERT IGNORE INTO users (user_id, unique_id, fname, lname, email, password, img, status) 
                                VALUES ({$demo_user_id}, 999999999, 'Demo', 'User', 'demo@demo.com', '', '', 'Afwezig')");
        }
        
        $user_id = $demo_user_id;
    } else {
        // Regular users - get their user_id from the database
        $unique_id_escaped = mysqli_real_escape_string($conn, $unique_id);
        $is_numeric = is_numeric($unique_id);
        $unique_id_sql = $is_numeric ? $unique_id : "'{$unique_id_escaped}'";

        $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = {$unique_id_sql}");
        
        if (!$user_query || mysqli_num_rows($user_query) == 0) {
            echo "error";
            exit;
        }
        
        $user_row = mysqli_fetch_assoc($user_query);
        $user_id = $user_row['user_id'];
    }

    // Check if reaction already exists for this user and message
    $check = mysqli_query($conn, "SELECT * FROM reactions WHERE user_id = {$user_id} AND msg_id = {$msg_id}");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE reactions SET emoji = '{$emoji}' WHERE user_id = {$user_id} AND msg_id = {$msg_id}");
    } else {
        mysqli_query($conn, "INSERT INTO reactions (user_id, msg_id, emoji) VALUES ({$user_id}, {$msg_id}, '{$emoji}')");
    }
    
    echo "success";
}
