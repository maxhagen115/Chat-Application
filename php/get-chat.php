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
            $dec = decryptthis($msg, $key);
            if ($row['outgoing_msg_id'] === $outgoing_id) {

                if ($row['msg'] && $row['msg_img'] == NULL) {
                    $output .= '<div class="chat outgoing">
                    <div class="details">
                        <p>' . $dec . '</p>
                    </div>
                    </div>';
                }

                if ($row['msg_img'] && $row['msg'] == NULL) {
                    $output .= '<div class="chat outgoing">
                    <div class="details">
                        <img src="php/images/user_msg_img/' . $pic . '">
                    </div>
                    </div>';
                }
            } else {
                $output .= '<div class="chat incomming">
                            <img src="php/images/' . $row['img'] . '" alt="">
                            <div class="details">
                                <p>' . $dec . '</p>
                            </div>
                            </div>';
            }
        }
        echo $output;
    }
} else {
    header("../login.php");
}
