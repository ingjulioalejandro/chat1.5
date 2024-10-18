<?php
    while($row = mysqli_fetch_assoc($query)){
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);

        // Verifica si hay mensajes disponibles, de lo contrario, muestra "No message available"
        if(mysqli_num_rows($query2) > 0) {
            $result = $row2['msg'];
        } else {
            $result = "No message available";
        }

        // Limita el mensaje a 28 caracteres
        if(strlen($result) > 28) {
            $msg = substr($result, 0, 28) . '...';
        } else {
            $msg = $result;
        }

        // Si el mensaje es del usuario actual, prepende "You: "
        if(isset($row2['outgoing_msg_id'])){
            if($outgoing_id == $row2['outgoing_msg_id']) {
                $you = "You: ";
            } else {
                $you = "";
            }
        } else {
            $you = "";
        }

        // Establece la clase "offline" si el usuario está desconectado
        if($row['status'] == "Offline now") {
            $offline = "offline";
        } else {
            $offline = "";
        }

        // Oculta al usuario actual de la lista
        if($outgoing_id == $row['unique_id']) {
            $hid_me = "hide";
        } else {
            $hid_me = "";
        }

        // Genera la salida HTML para cada usuario
        $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                    <div class="content '. $hid_me .'">
                    <img src="php/images/'. $row['img'] .'" alt="User Image">
                    <div class="details">
                        <span>'. $row['fname']. " " . $row['lname'] .'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
    }
?>
