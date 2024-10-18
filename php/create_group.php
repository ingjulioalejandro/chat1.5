<?php
    session_start();
    include_once "config.php";
    $creator_unique_id = $_SESSION['unique_id'];
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    
    if(!empty($group_name)){
        // Primero, obtén el user_id correspondiente al unique_id
        $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = '{$creator_unique_id}'");
        $user = mysqli_fetch_assoc($user_query);
        $creator_id = $user['user_id'];

        $sql = mysqli_query($conn, "INSERT INTO chatrooms (room_name, created_by, anyone_can_add)
                            VALUES ('{$group_name}', {$creator_unique_id}, 0)");
        if($sql){
            $group_id = mysqli_insert_id($conn);
            $sql2 = mysqli_query($conn, "INSERT INTO group_members (group_id, user_id) VALUES ({$group_id}, {$creator_id})");
            if($sql2){
                echo json_encode(["status" => "success", "group_id" => $group_id]);
            } else {
                echo "Error adding creator to group members.";
            }
        }else{
            echo "Error creating group.";
        }
    }else{
        echo "Group name is required!";
    }
?>
