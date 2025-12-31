    <?php
    session_start();
    if (isset($_SESSION['unique_id'])) {
        include_once "config.php";
        include_once "ai_user.php";

        $outgoing_id = $_POST['outgoing_id'];
        $incomming_id = $_POST['incomming_id'];
        $message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : '';
        $output = "";

        // Check if current user is a demo user
        $is_demo_user = isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true;

        // Escape IDs for SQL
        $outgoing_id_escaped = mysqli_real_escape_string($conn, $outgoing_id);
        $incomming_id_escaped = mysqli_real_escape_string($conn, $incomming_id);
        
        // Check if IDs are numeric
        $is_numeric_outgoing = is_numeric($outgoing_id);
        $is_numeric_incomming = is_numeric($incomming_id);

        $key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

        function decryptthis($data, $key)
        {
            if (empty($data)) return "";
            try {
                $encryption_key = base64_decode($key);
                list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
                return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
            } catch (Exception $e) {
                return "";
            }
        }

        // Build SQL queries with proper quoting
        $outgoing_sql = $is_numeric_outgoing ? $outgoing_id : "'{$outgoing_id_escaped}'";
        $incomming_sql = $is_numeric_incomming ? $incomming_id : "'{$incomming_id_escaped}'";

        $mark_read = "
            UPDATE messages 
            SET is_read = 1 
            WHERE outgoing_msg_id = {$incomming_sql} 
            AND incomming_msg_id = {$outgoing_sql}
            AND is_read = 0
        ";
        mysqli_query($conn, $mark_read);

        $sql = "SELECT messages.*, users.img, users.fname, users.lname, users.status 
                FROM messages
                LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (messages.outgoing_msg_id = {$outgoing_sql} AND messages.incomming_msg_id = {$incomming_sql})
                OR (messages.outgoing_msg_id = {$incomming_sql} AND messages.incomming_msg_id = {$outgoing_sql})
                ORDER BY messages.msg_id";

        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $msg = $row['msg'];
                $pic = $row['msg_img'];
                $dec = $msg ? decryptthis($msg, $key) : "";

                // Compare outgoing_msg_id with current user's ID to determine if they sent the message
                // outgoing_msg_id in the database = the person who SENT the message
                // outgoing_id from POST = the current user viewing the chat
                // So if they match, the current user sent this message
                
                // Get the outgoing_msg_id from messages table
                $row_outgoing_id = isset($row['outgoing_msg_id']) ? $row['outgoing_msg_id'] : null;
                
                // Check if this message is from AI user (AI messages should always be incoming)
                $isAIMessage = ($row_outgoing_id == AI_USER_ID || (string)$row_outgoing_id == (string)AI_USER_ID);
                
                // For demo users: if img is empty/null AND it's not an AI message,
                // it means the LEFT JOIN didn't find a user (demo user sent it)
                $isDemoUserMessage = (empty($row['img']) || $row['img'] === null) && !$isAIMessage;
                
                // If it's an AI message, it's always incoming (not from current user)
                if ($isAIMessage) {
                    $isSender = false;
                }
                // Ensure both values exist
                else if (empty($row_outgoing_id) && !$isDemoUserMessage) {
                    $isSender = false;
                } else {
                    // Convert both to strings and compare
                    $row_id_str = trim((string)$row_outgoing_id);
                    $outgoing_id_str = trim((string)$outgoing_id);
                    
                    // Special case: Demo user messages
                    // If current user is demo user AND message has no img (demo user sent it)
                    // AND it's not an AI message
                    if ($is_demo_user && $isDemoUserMessage) {
                        $isSender = true;
                    }
                    // Direct string comparison
                    else if ($row_id_str === $outgoing_id_str) {
                        $isSender = true;
                    }
                    // Fallback: if both are numeric, compare as integers
                    else if (is_numeric($row_id_str) && is_numeric($outgoing_id_str)) {
                        $isSender = ((int)$row_id_str === (int)$outgoing_id_str);
                    }
                    // Additional fallback: loose comparison
                    else {
                        $isSender = ($row_outgoing_id == $outgoing_id);
                    }
                }
                
                $direction = $isSender ? 'outgoing' : 'incoming';

                if ($row['deleted'] === 'sender' && $isSender) continue;

                $output .= '<div class="chat ' . $direction . '">';

                if (!$isSender && $msg && !$pic && $row['deleted'] !== 'both') {
                    // Handle AI user profile picture
                    if ($row['outgoing_msg_id'] == AI_USER_ID || (string)$row['outgoing_msg_id'] == (string)AI_USER_ID) {
                        $aiUser = getAIUser();
                        $output .= '<img src="' . $aiUser['img'] . '" alt="" class="profile-pic" loading="lazy">';
                    } else {
                        $output .= '<img src="php/images/' . $row['img'] . '" alt="" class="profile-pic" loading="lazy">';
                    }
                }

                $output .= '<div class="details" data-id="' . $row['msg_id'] . '">';

                if ($row['deleted'] === 'both') {
                    $output .= '<div><p><em>Dit bericht is verwijderd</em></p></div>';
                } else {
                    if ($pic && !$msg) {
                        $output .= '<div><img src="php/images/user_msg_img/' . $pic . '" class="chat-image"></div>';
                    } elseif ($msg && !$pic) {
                        $output .= '<div class="message-wrap">';

                        if (!empty(trim($dec))) {
                            $output .= '<p>' . $dec;
                            if ($row['edited']) {
                                $output .= ' <small>(bewerkt)</small>';
                            }
                            $output .= '</p>';
                        }

                        $output .= '<div class="reaction-bar" data-msg-id="' . $row['msg_id'] . '">
                            <span class="react-emoji" data-emoji="👍">👍</span>
                            <span class="react-emoji" data-emoji="❤️">❤️</span>
                        </div>';

                        $reaction_sql = "SELECT DISTINCT emoji FROM reactions WHERE msg_id = {$row['msg_id']}";
                        $reaction_query = mysqli_query($conn, $reaction_sql);

                        if (mysqli_num_rows($reaction_query) > 0) {
                            $output .= '<div class="reactions">';
                            while ($reaction_row = mysqli_fetch_assoc($reaction_query)) {
                                $output .= '<span class="reaction">' . $reaction_row['emoji'] . '</span>';
                            }
                            $output .= '</div>'; 
                        }

                        if ($isSender && $row['deleted'] === 'none') {
                            $output .= '
                            <div class="dropdown-container">
                                <span class="menu-trigger">⋮</span>
                                <ul class="dropdown-menu hidden">
                                    <li class="edit-option">Bewerk</li>
                                    <li class="delete-option">Verwijder</li>
                                </ul>
                            </div>';
                        }

                        $output .= '</div>'; 
                    }
                }

                $output .= '</div>'; 
                $output .= '</div>'; 
            }

            echo $output;
        }
    } else {
        header("location: ../login.php");
    }
