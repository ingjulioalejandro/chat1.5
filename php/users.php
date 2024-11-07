<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];


    $user_query = mysqli_query($conn, "SELECT user_id FROM users WHERE unique_id = {$outgoing_id}");
    $user = mysqli_fetch_assoc($user_query);
    $user_id = $user['user_id'];


    $last_messages_sql = "
        (SELECT 
            m.msg_id,
            m.room_id as chat_id,
            NULL as user_chat_id,
            m.msg,
            m.sent_at,
            m.outgoing_msg_id,
            u.fname as sender_name,
            'group' as type
        FROM messages m
        LEFT JOIN users u ON m.outgoing_msg_id = u.unique_id
        WHERE m.room_id IS NOT NULL
        AND m.msg_id IN (
            SELECT MAX(msg_id) 
            FROM messages 
            WHERE room_id IS NOT NULL 
            GROUP BY room_id
        ))
        UNION ALL
        (SELECT 
            m.msg_id,
            NULL as chat_id,
            CASE 
                WHEN m.outgoing_msg_id = {$outgoing_id} THEN m.incoming_msg_id
                ELSE m.outgoing_msg_id
            END as user_chat_id,
            m.msg,
            m.sent_at,
            m.outgoing_msg_id,
            NULL as sender_name,
            'user' as type
        FROM messages m
        WHERE m.room_id IS NULL
        AND (m.outgoing_msg_id = {$outgoing_id} OR m.incoming_msg_id = {$outgoing_id})
        AND m.msg_id IN (
            SELECT MAX(msg_id) 
            FROM messages 
            WHERE room_id IS NULL 
            AND (outgoing_msg_id = {$outgoing_id} OR incoming_msg_id = {$outgoing_id})
            GROUP BY 
                CASE 
                    WHEN outgoing_msg_id = {$outgoing_id} THEN incoming_msg_id
                    ELSE outgoing_msg_id
                END
        ))
        ORDER BY sent_at DESC";

    $last_messages = mysqli_query($conn, $last_messages_sql);
    $last_messages_data = [];
    
    while($msg = mysqli_fetch_assoc($last_messages)) {
        if($msg['type'] == 'group') {
            $last_messages_data['group_' . $msg['chat_id']] = $msg;
        } else {
            $last_messages_data['user_' . $msg['user_chat_id']] = $msg;
        }
    }


    $groups_sql = "SELECT DISTINCT
                    c.room_id as unique_id,
                    c.room_name as fname,
                    '' as lname,
                    'default.png' as img,
                    'Group' as status,
                    'group' as type
                FROM chatrooms c
                LEFT JOIN group_members gm ON c.room_id = gm.group_id
                WHERE gm.user_id = {$user_id} 
                OR c.created_by = {$outgoing_id}";


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

    $all_chats = [];


    while($row = mysqli_fetch_assoc($groups_query)) {
        $last_msg = isset($last_messages_data['group_' . $row['unique_id']]) 
                    ? $last_messages_data['group_' . $row['unique_id']] 
                    : null;
        
        $row['last_message'] = $last_msg ? $last_msg['msg'] : "No message available";
        $row['sender_name'] = $last_msg ? $last_msg['sender_name'] : "";
        $row['sent_at'] = $last_msg ? $last_msg['sent_at'] : "0000-00-00 00:00:00";
        
        $all_chats[] = $row;
    }


    while($row = mysqli_fetch_assoc($users_query)) {
        $last_msg = isset($last_messages_data['user_' . $row['unique_id']]) 
                    ? $last_messages_data['user_' . $row['unique_id']] 
                    : null;
        
        $row['last_message'] = $last_msg ? $last_msg['msg'] : "No message available";
        $row['outgoing_msg_id'] = $last_msg ? $last_msg['outgoing_msg_id'] : null;
        $row['sent_at'] = $last_msg ? $last_msg['sent_at'] : "0000-00-00 00:00:00";
        
        $all_chats[] = $row;
    }

 
    usort($all_chats, function($a, $b) {
        return strcmp($b['sent_at'], $a['sent_at']);
    });

    $output = "";

 
    foreach($all_chats as $row) {
        if($row['type'] == 'group') {
            $result = $row['last_message'];
            if($row['sender_name']) {
                $result = $row['sender_name'] . ': ' . $result;
            }
            
            $unread_sql = "SELECT COUNT(*) as unread_count 
                           FROM messages 
                           WHERE room_id = {$row['unique_id']} 
                           AND outgoing_msg_id != {$outgoing_id}
                           AND DeliveredStatus IS NULL";
            $unread_query = mysqli_query($conn, $unread_sql);
            $unread_count = mysqli_fetch_assoc($unread_query)['unread_count'];
            
            $output .= '<a href="group_chat.php?room_id='. $row['unique_id'] .'">
                        <div class="content">
                        <img src="php/images/default.png" alt="">
                        <div class="details">
                            <span>'. $row['fname'] .'</span>
                            <p>'. (strlen($result) > 28 ? substr($result, 0, 28) . '...' : $result) .'</p>
                        </div>
                        '. ($unread_count > 0 ? '<div class="unread-badge">'.$unread_count.'</div>' : '') .'
                        </div>
                        <div class="status-dot"><i class="fas fa-users"></i></div>
                    </a>';
        } else {
            $you = ($row['outgoing_msg_id'] == $outgoing_id) ? "You: " : "";
            $result = $you . $row['last_message'];
            $offline = ($row['status'] == "Offline now") ? "offline" : "";
            
            $unread_sql = "SELECT COUNT(*) as unread_count 
                           FROM messages 
                           WHERE incoming_msg_id = {$outgoing_id} 
                           AND outgoing_msg_id = {$row['unique_id']}
                           AND room_id IS NULL
                           AND DeliveredStatus IS NULL";
            $unread_query = mysqli_query($conn, $unread_sql);
            $unread_count = mysqli_fetch_assoc($unread_query)['unread_count'];
            
            $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                        <div class="content">
                        <img src="php/images/'. $row['img'] .'" alt="">
                        <div class="details">
                            <span>'. $row['fname']. " " . $row['lname'] .'</span>
                            <p>'. (strlen($result) > 28 ? substr($result, 0, 28) . '...' : $result) .'</p>
                        </div>
                        '. ($unread_count > 0 ? '<div class="unread-badge">'.$unread_count.'</div>' : '') .'
                        </div>
                        <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                    </a>';
        }
    }

    if(empty($output)){
        $output .= "No users are available to chat";
    }

    echo $output;
?>