<?php
session_start();
if (isset($_POST['msg_id'], $_POST['delete_for'], $_SESSION['unique_id'])) {
    include_once "config.php";

    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $delete_for = $_POST['delete_for'];
    $user_id = $_SESSION['unique_id'];

    $check = mysqli_query($conn, "SELECT * FROM messages WHERE msg_id = {$msg_id}");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);

        if ($row['outgoing_msg_id'] == $user_id) {
            if ($delete_for === "sender") {
                mysqli_query($conn, "UPDATE messages SET deleted = 'sender' WHERE msg_id = {$msg_id}");
            } elseif ($delete_for === "both") {
                mysqli_query($conn, "UPDATE messages SET deleted = 'both' WHERE msg_id = {$msg_id}");
            }
            echo "success";
        } else {
            echo "unauthorized";
        }
    } else {
        echo "not_found";
    }
} else {
    echo "invalid_request";
}
