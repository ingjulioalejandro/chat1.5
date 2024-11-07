<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
        $output = "";
        $sql = "SELECT m.*, u.img, u.fname, u.lname 
                FROM messages m 
                LEFT JOIN users u ON u.unique_id = m.outgoing_msg_id
                WHERE m.room_id = {$room_id} 
                ORDER BY m.msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                $time = date('h:i A', strtotime($row['dateTimeMsg']));
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'.$row['msg'].'</p>
                                    <span class="time">'.$time.'</span>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <span>'.$row['fname'].' '.$row['lname'].'</span>
                                    <p>'.$row['msg'].'</p>
                                    <span class="time">'.$time.'</span>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }
?>
