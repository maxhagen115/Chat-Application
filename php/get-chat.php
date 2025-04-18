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

            // Skip sender-deleted messages
            if ($row['deleted'] === 'sender' && $isSender) continue;

            $output .= '<div class="chat ' . $direction . '">';

            // Incoming profile pic for text-only messages
            if (!$isSender && $msg && !$pic && $row['deleted'] !== 'both') {
                $output .= '<img src="php/images/' . $row['img'] . '" alt="" class="profile-pic">';
            }

            $output .= '<div class="details" data-id="' . $row['msg_id'] . '">';

            // 🗑️ Show delete icon only for sender and if not deleted
            if ($isSender && $row['deleted'] === 'none') {
                $output .= '<span class="delete-msg" title="Verwijder">🗑️</span>';
            }

            // Show deleted placeholder or real message
            if ($row['deleted'] === 'both') {
                $output .= '<div><p><em>Dit bericht is verwijderd</em></p></div>';
            } else {
                if ($pic && !$msg) {
                    $output .= '<div><img src="php/images/user_msg_img/' . $pic . '" class="chat-image"></div>';
                } elseif ($msg && !$pic) {
                    $output .= '<div><p>' . $dec . '</p></div>';
                }
            }

            $output .= '</div>'; // .details
            $output .= '</div>'; // .chat
        }

        echo $output;
    }
} else {
    header("location: ../login.php");
}
