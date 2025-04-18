<?php
session_start();
include_once "config.php";

$outgoing_id = $_SESSION['unique_id'];
$incomming_id = mysqli_real_escape_string($conn, $_POST['user_id']);

$sql = mysqli_query($conn, "SELECT typing_to, status FROM users WHERE unique_id = {$incomming_id}");
$row = mysqli_fetch_assoc($sql);

if ($row['typing_to'] == $outgoing_id) {
  echo "Typen...";
} else {
  echo $row['status'];
}
?>
