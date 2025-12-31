<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    
    // Handle demo user (string ID) vs regular user (numeric ID)
    if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true) {
        // Demo user: show all users
        $sql = mysqli_query($conn, "SELECT * FROM users");
    } else {
        // Regular user: exclude current user
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id}");
    }
    
    $output = "";
    if (mysqli_num_rows($sql) == 0) {
        $output .= "Geen gebruikers zijn aanwezig";
    } elseif (mysqli_num_rows($sql) > 0) {
        include 'data.php';
    }

    echo $output;
