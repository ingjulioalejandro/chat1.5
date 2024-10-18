<?php
session_start();
include_once "config.php";
if(isset($_SESSION['unique_id'])){
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $anyone_can_add = isset($_POST['anyone_can_add']) ? 1 : 0;
    
    $sql = mysqli_query($conn, "UPDATE chatrooms SET room_name = '{$group_name}', anyone_can_add = {$anyone_can_add} 
                                WHERE room_id = {$group_id} AND created_by = {$_SESSION['unique_id']}");
    if($sql){
        echo "success";
    }else{
        echo "Error updating group settings.";
    }
}else{
    header("location: ../login.php");
}
?>

