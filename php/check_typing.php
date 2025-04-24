<?php
session_start();
include_once "config.php";

$outgoing_id = $_SESSION['unique_id'];
$incomming_id = mysqli_real_escape_string($conn, $_POST['user_id']);

$sql = mysqli_query($conn, "SELECT typing_to, status, last_online FROM users WHERE unique_id = {$incomming_id}");
$row = mysqli_fetch_assoc($sql);

if ($row['typing_to'] == $outgoing_id) {
    echo "Is aan het typen...";
} else {
    if ($row['status'] == 'Actief') {
        echo "Actief";
    } else {
        $lastOnline = new DateTime($row['last_online']);
        $now = new DateTime();

        if ($lastOnline->format('Y-m-d') == $now->format('Y-m-d')) {
            $seen = $lastOnline->format('H:i');
        } else {
            $formatter = new IntlDateFormatter(
                'nl_NL',
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                'Europe/Amsterdam',
                IntlDateFormatter::GREGORIAN,
                'd MMMM'
            );
            $seen = $formatter->format($lastOnline);
        }

        echo "Afwezig. Voor het laatst gezien op {$seen}";
    }
}
?>
