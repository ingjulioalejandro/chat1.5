<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);

    $sql = "SELECT * FROM users 
            WHERE NOT unique_id = '{$outgoing_id}'
            AND (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%') 
            AND user_id NOT IN (SELECT user_id FROM group_members WHERE group_id = {$group_id})";
    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $output .= '<a href="#" onclick="addMember('.$row['user_id'].'); return false;">
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
    }else{
        $output .= 'No user found related to your search term';
    }
    echo $output;
?>
