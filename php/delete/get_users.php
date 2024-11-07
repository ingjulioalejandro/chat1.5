<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_GET['group_id'])){
    $outgoing_id = $_SESSION['unique_id'];
    $group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
    
    $sql = "SELECT u.* FROM users u
            LEFT JOIN group_members gm ON u.user_id = gm.user_id AND gm.group_id = {$group_id}
            WHERE u.unique_id != '{$outgoing_id}' AND gm.user_id IS NULL
            ORDER BY u.user_id DESC";
    $query = mysqli_query($conn, $sql);
    $output = "";
    
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to add";
    }elseif(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $output .= '<a href="#" onclick="addMember(\''.$row['unique_id'].'\'); return false;">
                        <div class="content">
                        <img src="php/images/'.$row['img'].'" alt="">
                        <div class="details">
                            <span>'.$row['fname']. " " . $row['lname'].'</span>
                            <p>Click to add to group</p>
                        </div>
                        </div>
                        <div class="status-dot"><i class="fas fa-circle"></i></div>
                    </a>';
        }
    }
    echo $output;
}else{
    header("location: ../login.php");
}
?>
