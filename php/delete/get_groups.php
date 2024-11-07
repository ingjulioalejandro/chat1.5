<?php
session_start();
include_once "config.php";

$output = "<h2>Groups</h2>";
$sql = mysqli_query($conn, "SELECT * FROM chatrooms ORDER BY room_id DESC");
while($row = mysqli_fetch_assoc($sql)){
  $output .= '<div class="group">';
  $output .= '<a href="group_chat.php?room_id='.$row['room_id'].'">'.$row['room_name'].'</a>';
  if($_SESSION['unique_id'] == $row['created_by']){
    $output .= '<span class="creator-tag">Creator</span>';
  }
  $output .= '</div>';
}

echo $output;
?>

