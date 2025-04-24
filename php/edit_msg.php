<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $new_msg = mysqli_real_escape_string($conn, $_POST['new_msg']);

    $key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted_msg = openssl_encrypt($new_msg, 'aes-256-cbc', $encryption_key, 0, $iv);
    $encrypted_data = base64_encode($encrypted_msg . '::' . $iv);

    $sql = "UPDATE messages SET msg = '{$encrypted_data}', edited = 1 WHERE msg_id = {$msg_id}";
    echo mysqli_query($conn, $sql) ? "success" : "error";
}
?>
