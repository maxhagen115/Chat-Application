<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']);
    $incomming_id = mysqli_real_escape_string($conn, $_POST['incomming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $output = "";

    $key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

    function decryptthis($data, $key)
    {
        $encryption_key = base64_decode($key);
        list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    $sql = "SELECT * FROM messages
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incomming_msg_id = {$incomming_id})
            OR (outgoing_msg_id = {$incomming_id} AND incomming_msg_id = {$outgoing_id}) ORDER BY msg_id";

    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $msg = $row['msg'];
            $pic = $row['msg_img'];
            $dec = $msg ? decryptthis($msg, $key) : "";

            $isSender = $row['outgoing_msg_id'] === $outgoing_id;
            $direction = $isSender ? 'outgoing' : 'incoming';

            $output .= '<div class="chat ' . $direction . '">';

            // Show profile picture for incoming text only
            if (!$isSender && $msg && !$pic) {
                $output .= '<img src="php/images/' . $row['img'] . '" alt="" class="profile-pic">';
            }

            $output .= '<div class="details">';
            
            if ($pic && !$msg) {
                $output .= '<div><img src="php/images/user_msg_img/' . $pic . '" class="chat-image"></div>';
            } elseif ($msg && !$pic) {
                $output .= '<div><p>' . $dec . '</p></div>';
            }

            $output .= '</div></div>';
        }

        echo $output;
    }
} else {
    header("location: ../login.php");
}
