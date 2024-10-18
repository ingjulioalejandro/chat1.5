<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";

    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $outgoing_id = $_SESSION['unique_id'];

    // Verificar si se ha enviado un mensaje o un archivo adjunto
    if(!empty($message) || isset($_FILES['attachment'])){
        
        // Verificar si hay un archivo adjunto
        if(isset($_FILES['attachment']) && $_FILES['attachment']['size'] > 0){
            $file_name = time() . "_" . $_FILES['attachment']['name']; // Generar un nombre único
            $file_tmp = $_FILES['attachment']['tmp_name'];
            $file_destination = "uploads/" . $file_name;  // Guardar en "php/uploads"
            
            // Mover archivo a la carpeta de uploads
            if(move_uploaded_file($file_tmp, $file_destination)){
                // Insertar mensaje con archivo adjunto
                $sql = "INSERT INTO messages (room_id, outgoing_msg_id, msg, file_path) 
                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_name}')";
            }
        } else {
            // Insertar solo mensaje de texto
            $sql = "INSERT INTO messages (room_id, outgoing_msg_id, msg) 
                    VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')";
        }

        mysqli_query($conn, $sql);
    }
} else {
    header("location: ../login.php");
}
