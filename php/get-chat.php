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

        // Mark incoming messages as read
        $mark_read = "
            UPDATE messages 
            SET is_read = 1 
            WHERE outgoing_msg_id = {$incomming_id} 
            AND incomming_msg_id = {$outgoing_id}
            AND is_read = 0
        ";
        mysqli_query($conn, $mark_read);

        // Get messages between users
        $sql = "SELECT * FROM messages
                LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incomming_msg_id = {$incomming_id})
                OR (outgoing_msg_id = {$incomming_id} AND incomming_msg_id = {$outgoing_id})
                ORDER BY msg_id";

        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $msg = $row['msg'];
                $pic = $row['msg_img'];
                $dec = $msg ? decryptthis($msg, $key) : "";

                $isSender = $row['outgoing_msg_id'] === $outgoing_id;
                $direction = $isSender ? 'outgoing' : 'incoming';

                // Skip if sender deleted and viewing user is sender
                if ($row['deleted'] === 'sender' && $isSender) continue;

                $output .= '<div class="chat ' . $direction . '">';

                // Profile image for incoming text-only messages
                if (!$isSender && $msg && !$pic && $row['deleted'] !== 'both') {
                    $output .= '<img src="php/images/' . $row['img'] . '" alt="" class="profile-pic" loading="lazy">';
                }

                $output .= '<div class="details" data-id="' . $row['msg_id'] . '">';

                if ($row['deleted'] === 'both') {
                    $output .= '<div><p><em>Dit bericht is verwijderd</em></p></div>';
                } else {
                    if ($pic && !$msg) {
                        // Image-only
                        $output .= '<div><img src="php/images/user_msg_img/' . $pic . '" class="chat-image"></div>';
                    } elseif ($msg && !$pic) {
                        // Text-only
                        $output .= '<div class="message-wrap">';

                        if (!empty(trim($dec))) {
                            $output .= '<p>' . $dec;
                            if ($row['edited']) {
                                $output .= ' <small>(bewerkt)</small>';
                            }
                            $output .= '</p>';
                        }

                        // ✅ Reaction bar outside <p>
                        $output .= '<div class="reaction-bar" data-msg-id="' . $row['msg_id'] . '">
                            <span class="react-emoji" data-emoji="👍">👍</span>
                            <span class="react-emoji" data-emoji="❤️">❤️</span>
                        </div>';

                        // Show current reactions
                        $reaction_sql = "SELECT DISTINCT emoji FROM reactions WHERE msg_id = {$row['msg_id']}";
                        $reaction_query = mysqli_query($conn, $reaction_sql);

                        if (mysqli_num_rows($reaction_query) > 0) {
                            $output .= '<div class="reactions">';
                            while ($reaction_row = mysqli_fetch_assoc($reaction_query)) {
                                $output .= '<span class="reaction">' . $reaction_row['emoji'] . '</span>';
                            }
                            $output .= '</div>'; // .reactions
                        }

                        // Dropdown for edit/delete
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

                        $output .= '</div>'; // .message-wrap
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
