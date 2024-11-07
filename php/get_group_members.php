<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])) {
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    $current_user_id = $_SESSION['unique_id'];
    
    // Verificar si el usuario actual es el creador o tiene permisos
    $permission_check = mysqli_query($conn, "SELECT created_by FROM chatrooms WHERE room_id = {$room_id}");
    $room_info = mysqli_fetch_assoc($permission_check);
    $can_remove = ($current_user_id == $room_info['created_by']);
    
    $sql = "SELECT u.unique_id, u.fname, u.lname, u.img, u.status,
            CASE WHEN u.unique_id = c.created_by THEN 'creator' ELSE 'member' END as display_role
            FROM group_members gm 
            JOIN users u ON gm.user_id = u.user_id 
            JOIN chatrooms c ON gm.group_id = c.room_id
            WHERE gm.group_id = {$room_id} 
            ORDER BY 
                CASE 
                    WHEN u.unique_id = c.created_by THEN 1
                    ELSE 2
                END";
            
    $query = mysqli_query($conn, $sql);
    $output = "";
    
    if(mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_assoc($query)) {
            $status = ($row['status'] == "Active now") ? "" : "offline";
            $role_text = ucfirst($row['display_role']);
            
            $output .= '<div class="member-item" data-id="'.$row['unique_id'].'">
                        <div class="member-info">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <span>'. $row['fname']. " " . $row['lname'] .'</span>
                                <p class="role-tag '. $row['display_role'] .'">'. $role_text .'</p>
                            </div>
                        </div>
                        <div class="member-meta">
                            <div class="status-dot '. $status .'"><i class="fas fa-circle"></i></div>';
            
            // Mostrar botón de eliminar solo si tiene permisos y no es el creador
            if($can_remove && $row['display_role'] !== 'creator') {
                $output .= '<button class="remove-member" data-user-id="'.$row['unique_id'].'">
                            <i class="fas fa-times"></i>
                           </button>';
            }
            
            $output .= '</div></div>';
        }
    } else {
        $output .= '<div class="no-members">No members found</div>';
    }
    echo $output;
}
?>
