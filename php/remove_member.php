<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])) {
    $current_user_id = $_SESSION['unique_id'];
    $user_to_remove = mysqli_real_escape_string($conn, $_POST['user_id']);
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    
    // Verificar si el usuario actual tiene permisos
    $permission_check = mysqli_query($conn, "SELECT created_by FROM chatrooms WHERE room_id = {$room_id}");
    $room_info = mysqli_fetch_assoc($permission_check);
    
    if($current_user_id == $room_info['created_by']) {
        // Obtener user_id del usuario a eliminar
        $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = {$user_to_remove}");
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
}
?>
