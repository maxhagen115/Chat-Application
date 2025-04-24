<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
?>

<?php include "header.php"; ?>
<?php include "light_dark_switch.php"; ?>

<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <?php
        include_once "php/config.php";
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        }
        ?>
        <div class="content">
          <img class="profile-pic" onclick="enlargeImg()" id="profile_img" src="php/images/<?= $row['img'] ?> " />
          <div id="reset_btn" style="display:none;">
            <button class="reset_img" onclick="resetImg()">Verberg</button>
          </div>
          <div class="details">
            <span><?= $row['fname'] . " " . $row['lname'] ?></span><br>
          </div>
        </div>
        <a href="php/logout.php?logout_id=<?= $row['unique_id'] ?>" class="logout">Loguit</a>
      </header>
      <div class="search">
        <span class="text">Selecteer een user om mee te chatten</span>
        <input type="text" placeholder="Zoek user" />
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">

      </div>
    </section>
  </div>

  <script src="js/users.js"></script>

  <script>
    img = document.getElementById("profile_img");

    function enlargeImg() {
      const img = document.getElementById("profile_img");
      const resetBtn = document.getElementById("reset_btn");

      img.style.transform = "scale(4)";
      img.style.transition = "transform 0.25s ease";
      img.style.zIndex = "1001";
      img.style.position = "relative";

      resetBtn.style.display = "block";
      resetBtn.style.zIndex = "1002";
      resetBtn.style.position = "relative";
    }

    function resetImg() {
      const img = document.getElementById("profile_img");
      const resetBtn = document.getElementById("reset_btn");

      img.style.transform = "scale(1)";
      img.style.transition = "transform 0.25s ease";
      img.style.zIndex = "1";

      resetBtn.style.display = "none";
    }
  </script>
</body>

</html>