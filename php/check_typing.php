<?php
session_start();
include_once "config.php";
include_once "ai_user.php";

$outgoing_id = $_SESSION['unique_id'];
$incomming_id = mysqli_real_escape_string($conn, $_POST['user_id']);

// Get language from cookie
$lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'nl';

// Handle AI user
if ($incomming_id == AI_USER_ID) {
    $translations = [
        'nl' => 'Actief',
        'en' => 'Active'
    ];
    echo $translations[$lang] ?? $translations['nl'];
    exit;
}

$sql = mysqli_query($conn, "SELECT typing_to, status, last_online FROM users WHERE unique_id = {$incomming_id}");
$row = mysqli_fetch_assoc($sql);

if ($row['typing_to'] == $outgoing_id) {
    $translations = [
        'nl' => 'Is aan het typen...',
        'en' => 'Is typing...'
    ];
    echo $translations[$lang] ?? $translations['nl'];
} else {
    if ($row['status'] == 'Actief') {
        $translations = [
            'nl' => 'Actief',
            'en' => 'Active'
        ];
        echo $translations[$lang] ?? $translations['nl'];
    } else {
        $lastOnline = new DateTime($row['last_online']);
        $now = new DateTime();

        if ($lastOnline->format('Y-m-d') == $now->format('Y-m-d')) {
            $seen = $lastOnline->format('H:i');
        } else {
            $locale = $lang === 'en' ? 'en_US' : 'nl_NL';
            $formatter = new IntlDateFormatter(
                $locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                'Europe/Amsterdam',
                IntlDateFormatter::GREGORIAN,
                'd MMMM'
            );
            $seen = $formatter->format($lastOnline);
        }

        $translations = [
            'nl' => "Afwezig. Voor het laatst gezien op {$seen}",
            'en' => "Offline. Last seen on {$seen}"
        ];
        echo $translations[$lang] ?? $translations['nl'];
    }
}
?>
