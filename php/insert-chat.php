<?php
session_start();

$key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

function encryptthis($data, $key)
{
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

if (isset($_SESSION['unique_id'])) {

    include_once "config.php";

    $unique_id = $_SESSION['unique_id'];
    $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']);
    $incomming_id = mysqli_real_escape_string($conn, $_POST['incomming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $var = $_POST['message'];
    $enc = encryptthis($var, $key);

    if (!empty($message)) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incomming_msg_id, outgoing_msg_id, msg)
                                    VALUES ({$incomming_id}, {$outgoing_id}, '{$enc}')") or die();

        mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$outgoing_id}");
    }

    $pic = $_FILES['picture'];

    if (isset($_FILES['picture'])) {
        $img_name = $_FILES['picture']['name'];
        $img_size = $_FILES['picture']['size'];
        $tmp_name = $_FILES['picture']['tmp_name'];
        $error = $_FILES['picture']['error'];

        if ($error === 0) {
            if ($img_size < 125000) {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);

                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("MSG-", true) . '.' . $img_ex_lc;
                    $img_upload_path = 'images/user_msg_img/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);

                    $sql = mysqli_query($conn, "INSERT INTO messages (incomming_msg_id, outgoing_msg_id, msg_img)
                                                VALUES ({$incomming_id}, {$outgoing_id}, '{$new_img_name}')") or die();

                    mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$outgoing_id}");
                } else {
                    $_SESSION['melding'] = "Alleen foto bestand selecteren";
                }
            } else {
            }
        }
    }
} else {
    header("location: ../login.php");
}