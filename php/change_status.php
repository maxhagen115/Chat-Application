<?php
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $session_id = $_SESSION['unique_id'];
        if(isset($session_id)){

            $status_afwezig = "Afwezig";
            $status_actief = "Actief";

            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            $row = mysqli_fetch_assoc($sql);

            if($row['status'] == 'Actief'){
                $sql = mysqli_query($conn, "UPDATE users SET status = '{$status_afwezig}', last_online = now() WHERE unique_id = {$session_id}");
            }elseif($row['status'] == 'Afwezig'){
                $sql = mysqli_query($conn, "UPDATE users SET status = '{$status_actief}', last_online = now() WHERE unique_id = {$session_id}");
            }
            if($sql){
                header("location: ../users.php");
            }
        }else{
            header("location: ../login.php");
        }
    }else{
        header("location: ../login.php");
    }
?>