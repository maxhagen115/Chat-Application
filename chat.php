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
    <section class="chat-area">
      <header>
        <?php
        include_once "php/config.php";
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>

        <img onclick="enlargeImg()" id="profile_img" src="php/images/<?= $row['img'] ?> " />
        <div id="reset_btn" style="display:none;">
          <button class="reset_img" onclick="resetImg()">Verberg</button>
        </div>

        <div class="details">
          <span><?= $row['fname'] . " " . $row['lname'] ?></span>
          <?php if ($row['status'] == 'Actief') { ?>
            <p><?= $row['status'] ?></p>
          <?php } else { ?>
            <p><?= $row['status'], '.', ' Voor het laatst gezien op ', $row['last_online'] ?></p>
          <?php } ?>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <?php
      if (isset($_SESSION['melding'])) {
        echo '<div class="">' . $_SESSION['melding'] . '</div>';
        $_SESSION['melding'] = null;
      }
      ?>
      <form action="#" class="typing-area" autocomplete="off" enctype="multipart/form-data">
        <input type="text" name="outgoing_id" value="<?= $_SESSION['unique_id']; ?>" hidden>
        <input type="text" name="incomming_id" value="<?= $user_id; ?>" hidden>
        <label for="picture">
          <i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i>
        </label>
        <input type="file" id="picture" name="picture" class="input-file">
        <label class="file-name" id="file-name"></label>
        <input type="text" id="msg" name="message" class="input-field" placeholder="Typ een bericht hier...">
        <button name="test" id="test"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
    <?php
    if (isset($_SESSION['melding'])) {
      echo '<div class="alert alert-danger" role="alert">' . $_SESSION['melding'] . '</div>';
      $_SESSION['melding'] = null;
    }
    ?>
  </div>

  <script src="js/chat.js"></script>

</body>

<script>
  document.querySelector("#picture").onchange = function() {
    var string = document.querySelector("#file-name").textContent = this.files[0].name;
    var length = 20;
    var trimmedString = string.substring(0, length - 3) + "...";
    document.querySelector("#file-name").innerHTML = trimmedString;
    document.getElementById("msg").style.display = 'none';
  }

  img = document.getElementById("profile_img");

  function enlargeImg() {
    img.style.transform = "scale(7)";
    img.style.transition = "transform 0.25s ease";

    var x = document.getElementById("reset_btn");
    if (img.style.transform === "scale(7)") {
      if (x.style.display === "none") {
        x.style.display = "block";
      }
    } else {
      x.style.display = "none";
    }

  }

  function resetImg() {

    img.style.transform = "scale(1)";
    img.style.transition = "transform 0.25s ease";

    var x = document.getElementById("reset_btn");

    if (img.style.transform === "scale(1)") {
      x.style.display = "none";
    }
  }
</script>

</html>