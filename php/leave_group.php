<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])) {
    $outgoing_id = $_SESSION['unique_id'];
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    
    // Obtener user_id
    $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = {$outgoing_id}");
    $user = mysqli_fetch_assoc($user_query);
    $user_id = $user['user_id'];
    
    // Eliminar al usuario del grupo
    $sql = mysqli_query($conn, "DELETE FROM group_members 
                              WHERE user_id = {$user_id} 
                              AND group_id = {$room_id}");
    
    if($sql) {
        echo "success";
    }
}
?>
