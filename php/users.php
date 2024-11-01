<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];

    // Primero obtenemos los grupos del usuario (usando user_id de la tabla users)
    $groups_sql = "SELECT DISTINCT 
                    c.room_id as unique_id,
                    c.room_name as fname,
                    '' as lname,
                    'default.png' as img,
                    'Group' as status,
                    'group' as type,
                    c.created_at
                FROM chatrooms c
                INNER JOIN group_members gm ON c.room_id = gm.group_id
                INNER JOIN users u ON gm.user_id = u.user_id
                WHERE u.unique_id = {$outgoing_id}
                OR c.created_by = {$outgoing_id}
                ORDER BY c.created_at DESC";

    // Luego obtenemos los usuarios
    $users_sql = "SELECT 
                    unique_id,
                    fname,
                    lname,
                    img,
                    status,
                    'user' as type
                FROM users 
                WHERE NOT unique_id = {$outgoing_id}";

    $groups_query = mysqli_query($conn, $groups_sql);
    $users_query = mysqli_query($conn, $users_sql);

    $output = "";

    // Primero mostramos los grupos
    while($row = mysqli_fetch_assoc($groups_query)){
        $sql2 = "SELECT m.*, u.fname 
                FROM messages m
                LEFT JOIN users u ON m.outgoing_msg_id = u.unique_id
                WHERE m.room_id = {$row['unique_id']}
                ORDER BY m.msg_id DESC LIMIT 1";
        
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);
        
        if(mysqli_num_rows($query2) > 0) {
            $result = $row2['msg'];
            if(isset($row2['fname'])) {
                $result = $row2['fname'] . ': ' . $result;
            }
        } else {
            $result = "No message available";
        }
        
        (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;
        
        $output .= '<a href="group_chat.php?room_id='. $row['unique_id'] .'">
                    <div class="content">
                    <img src="php/images/default.png" alt="">
                    <div class="details">
                        <span>'. $row['fname'] .'</span>
                        <p>'. $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot"><i class="fas fa-users"></i></div>
                </a>';
    }

    // Luego mostramos los usuarios
    while($row = mysqli_fetch_assoc($users_query)){
        $sql2 = "SELECT * FROM messages 
                WHERE (incoming_msg_id = {$row['unique_id']} AND outgoing_msg_id = {$outgoing_id})
                OR (outgoing_msg_id = {$row['unique_id']} AND incoming_msg_id = {$outgoing_id})
                ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);
        (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="No message available";
        (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
        if(isset($row2['outgoing_msg_id'])){
            ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
        }else{
            $you = "";
        }
        ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
        
        $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                    <div class="content">
                    <img src="php/images/'. $row['img'] .'" alt="">
                    <div class="details">
                        <span>'. $row['fname']. " " . $row['lname'] .'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
    }

    if(empty($output)){
        $output .= "No users are available to chat";
    }

    echo $output;
?>