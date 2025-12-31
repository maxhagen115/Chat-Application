<?php
    session_start();
    include_once "config.php";
    include_once "ai_user.php";
    
    if (!isset($_SESSION['unique_id'])) {
        echo "Geen gebruikers zijn aanwezig";
        exit;
    }
    
    $outgoing_id = $_SESSION['unique_id'];
    
    // Escape outgoing_id for SQL queries
    $outgoing_id_escaped = mysqli_real_escape_string($conn, $outgoing_id);
    $is_demo_user = isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true;
    $is_numeric_id = is_numeric($outgoing_id);
    
    // Handle demo user (string ID) vs regular user (numeric ID)
    // Always exclude the placeholder demo user (user_id = 999999) used for reactions
    if ($is_demo_user) {
        // Demo user: show all users except the placeholder demo user
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE user_id != 999999");
    } else {
        // Regular user: exclude current user and placeholder demo user (handle both numeric and string IDs)
        if ($is_numeric_id) {
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} AND user_id != 999999");
        } else {
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE NOT unique_id = '{$outgoing_id_escaped}' AND user_id != 999999");
        }
    }
    
    // Check for SQL errors
    if (!$sql) {
        error_log("SQL Error in users.php: " . mysqli_error($conn));
        echo "Geen gebruikers zijn aanwezig";
        exit;
    }
    
    $output = "";
    
    // Define decrypt function (needed for AI user even if no regular users)
    $key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
    
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
    
    // Always include AI user FIRST at the top
    $aiUser = getAIUser();
    
    // Get last message with AI user
    // Handle both numeric and string outgoing_id
    if (is_numeric($outgoing_id)) {
        $aiSql2 = "SELECT * FROM messages WHERE 
                    (incomming_msg_id = " . AI_USER_ID . " OR outgoing_msg_id = " . AI_USER_ID . ") 
                    AND (outgoing_msg_id = {$outgoing_id} OR incomming_msg_id = {$outgoing_id}) 
                    ORDER BY msg_id DESC LIMIT 1";
    } else {
        $aiSql2 = "SELECT * FROM messages WHERE 
                    (incomming_msg_id = " . AI_USER_ID . " OR outgoing_msg_id = " . AI_USER_ID . ") 
                    AND (outgoing_msg_id = '{$outgoing_id_escaped}' OR incomming_msg_id = '{$outgoing_id_escaped}') 
                    ORDER BY msg_id DESC LIMIT 1";
    }
    $aiQuery2 = mysqli_query($conn, $aiSql2);
    $aiRow2 = mysqli_fetch_assoc($aiQuery2);
    
    $rawMsg = $aiRow2['msg'] ?? null;
    $rawImg = $aiRow2['msg_img'] ?? null;
    
    if ($rawMsg == NULL && $rawImg != NULL) {
        $result = "Bijlage";
    } else {
        $dec = $rawMsg ? decryptthis($rawMsg, $key) : "";
        $result = ($aiRow2 && mysqli_num_rows($aiQuery2) > 0) ? $dec : "Geen bericht beschikbaar";
    }
    
    (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;
    
    if (isset($aiRow2['outgoing_msg_id'])) {
        ($outgoing_id == $aiRow2['outgoing_msg_id'] || (string)$outgoing_id == (string)$aiRow2['outgoing_msg_id']) ? $you = "Jij: " : $you = "";
    } else {
        $you = "";
    }
    
    // AI is always active
    $offline = "";
    
    // Check unread messages from AI
    if (is_numeric($outgoing_id)) {
        $check_unread = mysqli_query($conn, "
            SELECT COUNT(*) as total_unread 
            FROM messages 
            WHERE outgoing_msg_id = " . AI_USER_ID . " 
            AND incomming_msg_id = {$outgoing_id}
            AND is_read = 0
        ");
    } else {
        $check_unread = mysqli_query($conn, "
            SELECT COUNT(*) as total_unread 
            FROM messages 
            WHERE outgoing_msg_id = " . AI_USER_ID . " 
            AND incomming_msg_id = '{$outgoing_id_escaped}'
            AND is_read = 0
        ");
    }
    $unread_result = mysqli_fetch_assoc($check_unread);
    $unread = $unread_result ? $unread_result['total_unread'] : 0;
    
    $unreadDot = ($unread > 0) ? '<span class="inline-dot"></span> ' : '';
    
    // Get language for AI name
    $lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'nl';
    $aiFname = 'AI';
    $aiLname = 'Chat';
    
    // Build AI user HTML first (will be at top)
    $aiOutput = '<a href="chat.php?user_id=' . AI_USER_ID . '">
                    <div class="content">
                        <img src="' . $aiUser['img'] . '" alt="" style="border: 2px solid #4a90e2;">
                        <div class="details" loading="lazy">
                            <span>' . $aiFname . " " . $aiLname . '</span>
                            <p>' . $unreadDot . $you . $msg . '</p>
                        </div>
                    </div>
                    <div class="status-dot"><i class="fas fa-circle"></i></div>
                </a>';
    
    // Process regular users after AI user
    if (mysqli_num_rows($sql) > 0) {
        // Make sure data.php has access to the variables it needs
        $GLOBALS['outgoing_id'] = $outgoing_id;
        $GLOBALS['outgoing_id_escaped'] = $outgoing_id_escaped;
        $GLOBALS['is_numeric_id'] = $is_numeric_id;
        include 'data.php';
    }
    
    // Prepend AI user to output (so it appears first)
    $output = $aiOutput . $output;
    
    if (mysqli_num_rows($sql) == 0 && empty($output)) {
        $output .= "Geen gebruikers zijn aanwezig";
    }

    echo $output;
