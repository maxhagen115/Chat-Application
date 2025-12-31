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
    include_once "ai_user.php";

    $unique_id = $_SESSION['unique_id'];
    $outgoing_id = $_POST['outgoing_id'];
    $incomming_id = $_POST['incomming_id'];
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    // Escape IDs for SQL
    $outgoing_id_escaped = mysqli_real_escape_string($conn, $outgoing_id);
    $incomming_id_escaped = mysqli_real_escape_string($conn, $incomming_id);
    
    // Check if IDs are numeric
    $is_numeric_outgoing = is_numeric($outgoing_id);
    $is_numeric_incomming = is_numeric($incomming_id);
    
    // Build SQL values with proper quoting
    $outgoing_sql = $is_numeric_outgoing ? $outgoing_id : "'{$outgoing_id_escaped}'";
    $incomming_sql = $is_numeric_incomming ? $incomming_id : "'{$incomming_id_escaped}'";

    $var = $message;
    $enc = encryptthis($var, $key);

    if (!empty($message)) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incomming_msg_id, outgoing_msg_id, msg)
                                    VALUES ({$incomming_sql}, {$outgoing_sql}, '{$enc}')") or die();

        // Update user status (skip for demo user)
        if (!isset($_SESSION['demo_user']) || !$_SESSION['demo_user']) {
            $update_sql = $is_numeric_outgoing ? $outgoing_id : "'{$outgoing_id_escaped}'";
            mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$update_sql}");
        }
        
        // Check if message is to AI user and auto-reply
        if ($incomming_id == AI_USER_ID || (string)$incomming_id == (string)AI_USER_ID) {
            // Get the original message for AI response
            $decryptedMessage = $var;
            $aiResponse = generateAIResponse($decryptedMessage);
            $encryptedResponse = encryptthis($aiResponse, $key);
            
            // Insert AI response (AI sends message to user) - add small delay simulation
            usleep(500000); // 0.5 second delay to simulate thinking
            mysqli_query($conn, "INSERT INTO messages (incomming_msg_id, outgoing_msg_id, msg)
                                VALUES ({$outgoing_sql}, " . AI_USER_ID . ", '{$encryptedResponse}')") or die();
        }
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
                                                VALUES ({$incomming_sql}, {$outgoing_sql}, '{$new_img_name}')") or die();

                    if (!isset($_SESSION['demo_user']) || !$_SESSION['demo_user']) {
                        $update_sql = $is_numeric_outgoing ? $outgoing_id : "'{$outgoing_id_escaped}'";
                        mysqli_query($conn, "UPDATE users SET status = 'Actief', last_online = NOW() WHERE unique_id = {$update_sql}");
                    }
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