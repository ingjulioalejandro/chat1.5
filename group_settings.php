<?php 
session_start();
include_once "php/config.php";
if(!isset($_SESSION['unique_id'])){
  header("location: login.php");
}
$group_id = $_GET['group_id'];
$sql = mysqli_query($conn, "SELECT * FROM chatrooms WHERE room_id = {$group_id} AND created_by = {$_SESSION['unique_id']}");
if(mysqli_num_rows($sql) == 0){
  header("location: users.php");
}
$row = mysqli_fetch_assoc($sql);
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>Group Settings</header>
      <form action="#" method="POST" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Group Name</label>
          <input type="text" name="group_name" value="<?php echo $row['room_name']; ?>" required>
        </div>
        <div class="field">
          <input type="checkbox" id="anyone_can_add" name="anyone_can_add" <?php echo $row['anyone_can_add'] ? 'checked' : ''; ?>>
          <label for="anyone_can_add">Allow any member to add new users</label>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Update Settings">
        </div>
      </form>
    </section>
  </div>

  <script src="javascript/group_settings.js"></script>
</body>
</html>
