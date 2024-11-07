<?php
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
?>
