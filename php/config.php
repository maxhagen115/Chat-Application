<?php
error_reporting(0);
ini_set('display_errors', 0);

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "ChatApp";

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn) {
  error_log("Database connection failed: " . mysqli_connect_error());
  
  die("Er is een probleem met de database verbinding.");
}