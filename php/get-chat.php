<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $is_group = isset($_POST['is_group']) ? $_POST['is_group'] : 'false';

        if($is_group == 'true') {
            $sql = "SELECT m.*, u.img, u.fname, u.lname FROM messages m 
                    LEFT JOIN users u ON u.unique_id = m.outgoing_msg_id
                    WHERE m.room_id = {$incoming_id} 
                    ORDER BY m.msg_id";
        } else {
            $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                    WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                    OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        }
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                $time = date('h:i A', strtotime($row['dateTimeMsg']));
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">';
                    if(!empty($row['file_path'])){
                        $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                        $file_icon = getFileIcon($file_ext);
                        $file_name = !empty($row['file_name']) ? $row['file_name'] : basename($row['file_path']);
                        $output .= '<p><a href="'.$row['file_path'].'" target="_blank"><i class="'.$file_icon.'"></i> '.$file_name.'</a></p>';
                    }
                    $output .= '<p>'.$row['msg'].'</p>
                                <span class="time">'.$time.'</span>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">';
                    if(!empty($row['file_path'])){
                        $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                        $file_icon = getFileIcon($file_ext);
                        $output .= '<p><a href="'.$row['file_path'].'" target="_blank"><i class="'.$file_icon.'"></i> '.$row['file_name'].'</a></p>';
                    }
                    $output .= '<p>'.$row['msg'].'</p>
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

    function getFileIcon($extension) {
        $icon_classes = [
            'pdf' => 'far fa-file-pdf',
            'doc' => 'far fa-file-word',
            'docx' => 'far fa-file-word',
            'xls' => 'far fa-file-excel',
            'xlsx' => 'far fa-file-excel',
            'txt' => 'far fa-file-alt',
            'csv' => 'far fa-file-csv',
            'zip' => 'far fa-file-archive',
            'rar' => 'far fa-file-archive',
            'mp3' => 'far fa-file-audio',
            'mp4' => 'far fa-file-video',
            'avi' => 'far fa-file-video',
            'mov' => 'far fa-file-video',
            'jpg' => 'far fa-file-image',
            'jpeg' => 'far fa-file-image',
            'png' => 'far fa-file-image',
            'gif' => 'far fa-file-image'
        ];

        return isset($icon_classes[strtolower($extension)]) ? $icon_classes[strtolower($extension)] : 'far fa-file';
    }
?>
