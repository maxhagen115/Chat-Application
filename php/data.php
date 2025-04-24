<?php
error_reporting(E_ERROR | E_PARSE);
$key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

function decryptthis($data, $key)
{
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

$output = "";

while ($row = mysqli_fetch_assoc($sql)) {
    $sql2 = "SELECT * FROM messages WHERE 
                (incomming_msg_id = {$row['unique_id']} OR outgoing_msg_id = {$row['unique_id']}) 
                AND (outgoing_msg_id = {$outgoing_id} OR incomming_msg_id = {$outgoing_id}) 
                ORDER BY msg_id DESC LIMIT 1";
    $query2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($query2);

    $rawMsg = $row2['msg'];
    $rawImg = $row2['msg_img'];

    if ($rawMsg == NULL && $rawImg != NULL) {
        $result = "Bijlage";
    } else {
        $dec = decryptthis($rawMsg, $key);
        $result = (mysqli_num_rows($query2) > 0) ? $dec : "Geen bericht beschikbaar";
    }

    (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;

    if (isset($row2['outgoing_msg_id'])) {
        ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "Jij: " : $you = "";
    } else {
        $you = "";
    }

    ($row['status'] == "Afwezig") ? $offline = "offline" : $offline = "";
    ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    $check_unread = mysqli_query($conn, "
        SELECT COUNT(*) as total_unread 
        FROM messages 
        WHERE outgoing_msg_id = {$row['unique_id']} 
        AND incomming_msg_id = {$outgoing_id}
        AND is_read = 0
    ");
    $unread = mysqli_fetch_assoc($check_unread)['total_unread'];

    $unreadDot = ($unread > 0) ? '<span class="inline-dot"></span> ' : '';

    $output .= '<a href="chat.php?user_id=' . $row['unique_id'] . '">
                    <div class="content">
                        <img src="php/images/' . $row['img'] . '" alt="">
                        <div class="details" loading="lazy">
                            <span>' . $row['fname'] . " " . $row['lname'] . '</span>
                            <p>' . $unreadDot . $you . $msg . '</p>
                        </div>
                    </div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
}
