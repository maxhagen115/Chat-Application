<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
?>

<?php include "header.php"; ?>
<?php include "light_dark_switch.php"; ?>
<?php include "language_switch.php"; ?>

<body>
  <div class="wrapper users-wrapper">
    <section class="users">
      <header>
        <?php
        // Check if demo user
        if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true) {
          $row = [
            'unique_id' => $_SESSION['unique_id'],
            'fname' => $_SESSION['demo_fname'],
            'lname' => $_SESSION['demo_lname'],
            'img' => 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 150 150\'%3E%3Ccircle cx=\'75\' cy=\'75\' r=\'75\' fill=\'%23e0e0e0\'/%3E%3Ccircle cx=\'75\' cy=\'60\' r=\'20\' fill=\'%23999\'/%3E%3Cpath d=\'M75 85 C55 85 40 95 40 110 L40 120 L110 120 L110 110 C110 95 95 85 75 85 Z\' fill=\'%23999\'/%3E%3C/svg%3E'
          ];
        } else {
          include_once "php/config.php";
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
          if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
          }
        }
        ?>
        <div class="content">
          <img class="profile-pic" onclick="enlargeImg()" id="profile_img" src="<?= isset($_SESSION['demo_user']) && $_SESSION['demo_user'] ? $row['img'] : 'php/images/' . $row['img'] ?> " />
          <div id="reset_btn" style="display:none;">
            <button class="reset_img" onclick="resetImg()" data-i18n="users.hide">Verberg</button>
          </div>
          <div class="details">
            <?php if (isset($_SESSION['demo_user']) && $_SESSION['demo_user'] === true): ?>
              <span><span data-i18n="demo.fname">Demo</span> <span data-i18n="demo.lname">Gebruiker</span></span><br>
            <?php else: ?>
              <span><?= $row['fname'] . " " . $row['lname'] ?></span><br>
            <?php endif; ?>
          </div>
        </div>
        <a href="php/logout.php?logout_id=<?= $row['unique_id'] ?>" class="logout" data-i18n="users.logout">Loguit</a>
      </header>
      <div class="search">
        <span class="text" data-i18n="users.selectperson">Selecteer een persoon om mee te chatten</span>
        <input type="text" data-i18n-placeholder="users.search.placeholder" placeholder="Zoek een persoon" />
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">

      </div>
    </section>
  </div>

  <script src="js/translations.js"></script>
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