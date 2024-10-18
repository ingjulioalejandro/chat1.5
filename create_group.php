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
    <section class="form signup">
      <header>Create a New Group</header>
      <form action="#" method="POST" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Group Name</label>
          <input type="text" name="group_name" placeholder="Enter group name" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Create Group">
        </div>
      </form>
    </section>
  </div>

  <script src="javascript/create_group.js"></script>
</body>
</html>
