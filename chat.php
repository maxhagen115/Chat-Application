<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-mart@5.4.0/dist/browser.css" />

  <script src="https://cdn.jsdelivr.net/npm/emoji-mart@5.4.0/dist/browser.js"></script>
</head>
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

        <img class="profile-pic" onclick="enlargeImg()" id="profile_img" src="php/images/<?= $row['img'] ?> " />
        <div id="reset_btn" style="display:none;">
          <button class="reset_img" onclick="resetImg()">Verberg</button>
        </div>

        <div class="details">
          <span><?= $row['fname'] . " " . $row['lname'] ?></span>
          <p id="typing-status">
            <?php
            $lastOnline = new DateTime($row['last_online']);
            $now = new DateTime();
            $interval = $now->getTimestamp() - $lastOnline->getTimestamp();

            if ($row['status'] == 'Actief' && $interval < 10) { // Only "Actief" if seen in last 10 seconds
              echo "Actief";
            } else {
              if ($lastOnline->format('Y-m-d') == $now->format('Y-m-d')) {
                $seen = $lastOnline->format('H:i');
              } else {
                $formatter = new IntlDateFormatter(
                  'nl_NL',
                  IntlDateFormatter::NONE,
                  IntlDateFormatter::NONE,
                  'Europe/Amsterdam',
                  IntlDateFormatter::GREGORIAN,
                  'd MMMM'
                );
                $seen = $formatter->format($lastOnline);
              }

              echo "Afwezig. Voor het laatst gezien op {$seen}";
            }

            ?>
          </p>


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
        <label for="picture" style="cursor: pointer;">
          <i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i>
        </label>
        <input type="file" id="picture" name="picture" class="input-file">
        <label class="file-name" id="file-name"></label>
        <div class="emoji-picker-container">
          <button id="emoji-btn" type="button">😊</button>
        </div>
        <div id="emoji-picker" style="position: absolute; bottom: 60px; right: 20px; display: none; z-index: 1000;"></div>
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

  <div id="imageModal" class="image-modal hidden">
    <div class="image-wrapper">
      <img id="enlargedImage" src="" alt="Chat Image" />

      <button id="prevImage" class="image-nav left">&#10094;</button>
      <button id="nextImage" class="image-nav right">&#10095;</button>

      <a id="downloadImage" href="#" download class="download-btn" title="Download" rel="noopener noreferrer">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 16L8 12h3V4h2v8h3l-4 4zm-8 2v2h16v-2H4z" />
        </svg>
      </a>
    </div>
  </div>


  <div id="deleteModal" class="modal hidden">
    <div class="modal-content">
      <p class="modal-title">Bericht verwijderen</p>
      <button class="modal-btn" data-action="sender">Verwijder voor mij</button>
      <button class="modal-btn" data-action="both">Verwijder voor iedereen</button>
      <button class="modal-cancel">Annuleren</button>
    </div>
  </div>

  <script>
    const typingStatus = document.getElementById("typing-status");
    const incomming_id = <?= $user_id ?>;

    setInterval(() => {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "php/check_typing.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.onload = () => {
        if (xhr.status === 200) {
          typingStatus.textContent = xhr.responseText;
        }
      };
      xhr.send("user_id=" + incomming_id);
    }, 1000);
  </script>

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
    img.style.transform = "scale(4)";
    img.style.transition = "transform 0.25s ease";

    var x = document.getElementById("reset_btn");
    if (img.style.transform === "scale(4)") {
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