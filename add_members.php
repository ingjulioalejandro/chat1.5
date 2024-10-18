<?php 
session_start();
include_once "php/config.php";
if(!isset($_SESSION['unique_id'])){
  header("location: login.php");
}
$group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <img src="php/images/<?php echo $row['img']; ?>" alt="">
          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
        <a href="group_chat.php?room_id=<?php echo $group_id; ?>" class="back-icon"><i class="fas fa-arrow-left"></i></a>
      </header>
      <div class="search">
        <span class="text">Select a user to add to the group</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
        
      </div>
    </section>
  </div>

  <script src="javascript/add_members.js"></script>

</body>
</html>
