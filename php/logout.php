<?php
    session_start();
    if(isset($_SESSION['unique_id'])){
        // Handle demo user logout
        if(isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true){
            session_unset();
            session_destroy();
            header("location: ../login.php");
            exit;
        }
        
        // Regular user logout
        include_once "config.php";
        $logout_id = mysqli_real_escape_string($conn, $_GET['logout_id']);
        if(isset($logout_id)){
            $status = "Afwezig";
            $sql = mysqli_query($conn, "UPDATE users SET status = '{$status}', last_online = now() WHERE unique_id = {$logout_id}");
            if($sql){
                session_unset();
                session_destroy();
                header("location: ../login.php");
            }
        }else{
            header("location: ../users.php");
        }
    }else{
        header("location: ../login.php");
    }
?>