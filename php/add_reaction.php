<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    // Get the user's unique_id from the session
    $unique_id = $_SESSION['unique_id'];
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $emoji = mysqli_real_escape_string($conn, $_POST['emoji']);

    // Fetch the actual user_id (primary key) for this unique_id
    $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = {$unique_id}");
    $user_row = mysqli_fetch_assoc($user_query);
    $user_id = $user_row['user_id'];  // This is the foreign key in reactions

    // Check if this user already reacted to this message
    $check = mysqli_query($conn, "SELECT * FROM reactions WHERE user_id = {$user_id} AND msg_id = {$msg_id}");

    if (mysqli_num_rows($check) > 0) {
        // Update existing reaction
        mysqli_query($conn, "UPDATE reactions SET emoji = '{$emoji}' WHERE user_id = {$user_id} AND msg_id = {$msg_id}");
    } else {
        // Insert new reaction
        mysqli_query($conn, "INSERT INTO reactions (user_id, msg_id, emoji) VALUES ({$user_id}, {$msg_id}, '{$emoji}')");
    }

    echo "success";
} else {
    echo "unauthorized";
}
