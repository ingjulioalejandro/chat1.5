<?php 
session_start();
include_once "php/config.php";
if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
}
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
          $sql = mysqli_query($conn, "SELECT * FROM chatrooms WHERE room_id = {$room_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/default.png" alt="">
        <div class="details">
          <span><?php echo $row['room_name'] ?></span>
          <p><?php echo $row['created_by'] == $_SESSION['unique_id'] ? 'Creator' : 'Member'; ?></p>
        </div>
        <div class="dropdown">
          <button class="dropbtn"><i class="fas fa-ellipsis-v"></i></button>
          <div class="dropdown-content">
            <?php if($row['created_by'] == $_SESSION['unique_id'] || $row['anyone_can_add']): ?>
              <a href="add_members.php?group_id=<?php echo $room_id; ?>">Add Member</a>
            <?php endif; ?>
            <?php if($row['created_by'] == $_SESSION['unique_id']): ?>
              <a href="group_settings.php?group_id=<?php echo $room_id; ?>">Group Settings</a>
              <a href="#" id="deleteGroup" data-group-id="<?php echo $room_id; ?>">Delete Group</a>
            <?php endif; ?>
          </div>
        </div>
      </header>
      <div class="chat-box">
        <!-- Messages will be loaded here -->
      </div>
      <form action="#" class="typing-area" enctype="multipart/form-data">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $room_id; ?>" hidden>
        <input type="file" name="attachment" id="attachment" hidden>
        <label for="attachment" class="attach-btn"><i class="fas fa-paperclip"></i></label>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script>
    const roomId = <?php echo $room_id; ?>;
  </script>
  <script src="javascript/group_chat.js"></script>
</body>
</html>
