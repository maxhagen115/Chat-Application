<?php
session_start();

// Set demo user session data
$_SESSION['unique_id'] = 'demo_user_999999999';
$_SESSION['demo_user'] = true;
$_SESSION['demo_fname'] = 'Demo';
$_SESSION['demo_lname'] = 'Gebruiker';
$_SESSION['demo_img'] = 'default-profile.png';

// Redirect to users page
header("location: ../users.php");
exit;
?>

