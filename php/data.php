<?php
error_reporting(E_ERROR | E_PARSE);
$key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

// Get variables from global scope if needed
$outgoing_id = isset($GLOBALS['outgoing_id']) ? $GLOBALS['outgoing_id'] : $outgoing_id;
$outgoing_id_escaped = isset($GLOBALS['outgoing_id_escaped']) ? $GLOBALS['outgoing_id_escaped'] : mysqli_real_escape_string($conn, $outgoing_id);
$is_numeric_id = isset($GLOBALS['is_numeric_id']) ? $GLOBALS['is_numeric_id'] : is_numeric($outgoing_id);

if (!function_exists('decryptthis')) {
    function decryptthis($data, $key) {
        if (empty($data)) return "";
        try {
            $encryption_key = base64_decode($key);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        } catch (Exception $e) {
            return "";
        }
    }
}

$output = "";

while ($row = mysqli_fetch_assoc($sql)) {
    // Skip the placeholder demo user (user_id = 999999) used for reactions
    if ($row['user_id'] == 999999) {
        continue;
    }
    
    // Handle both numeric and string IDs in SQL queries
    if ($is_numeric_id) {
        $sql2 = "SELECT * FROM messages WHERE 
                    (incomming_msg_id = {$row['unique_id']} OR outgoing_msg_id = {$row['unique_id']}) 
                    AND (outgoing_msg_id = {$outgoing_id} OR incomming_msg_id = {$outgoing_id}) 
                    ORDER BY msg_id DESC LIMIT 1";
    } else {
        $row_id_escaped = mysqli_real_escape_string($conn, $row['unique_id']);
        $sql2 = "SELECT * FROM messages WHERE 
                    (incomming_msg_id = {$row['unique_id']} OR outgoing_msg_id = {$row['unique_id']}) 
                    AND (outgoing_msg_id = '{$outgoing_id_escaped}' OR incomming_msg_id = '{$outgoing_id_escaped}') 
                    ORDER BY msg_id DESC LIMIT 1";
    }
    $query2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($query2);

    $rawMsg = $row2['msg'] ?? null;
    $rawImg = $row2['msg_img'] ?? null;

    if ($rawMsg == NULL && $rawImg != NULL) {
        $result = "Bijlage";
    } else {
        $dec = $rawMsg ? decryptthis($rawMsg, $key) : "";
        $result = ($row2 && mysqli_num_rows($query2) > 0) ? $dec : "Geen bericht beschikbaar";
    }

    (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;

    if (isset($row2['outgoing_msg_id'])) {
        ($outgoing_id == $row2['outgoing_msg_id'] || (string)$outgoing_id == (string)$row2['outgoing_msg_id']) ? $you = "Jij: " : $you = "";
    } else {
        $you = "";
    }

    ($row['status'] == "Afwezig") ? $offline = "offline" : $offline = "";
    ($outgoing_id == $row['unique_id'] || (string)$outgoing_id == (string)$row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    // Handle unread count query for both numeric and string IDs
    if ($is_numeric_id) {
        $check_unread = mysqli_query($conn, "
            SELECT COUNT(*) as total_unread 
            FROM messages 
            WHERE outgoing_msg_id = {$row['unique_id']} 
            AND incomming_msg_id = {$outgoing_id}
            AND is_read = 0
        ");
    } else {
        $row_id_escaped = mysqli_real_escape_string($conn, $row['unique_id']);
        $check_unread = mysqli_query($conn, "
            SELECT COUNT(*) as total_unread 
            FROM messages 
            WHERE outgoing_msg_id = {$row['unique_id']} 
            AND incomming_msg_id = '{$outgoing_id_escaped}'
            AND is_read = 0
        ");
    }
    $unread_result = mysqli_fetch_assoc($check_unread);
    $unread = $unread_result ? $unread_result['total_unread'] : 0;

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
