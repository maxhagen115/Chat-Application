<?php
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include "header.php"; ?>
  <body>
    <div class="wrapper">
      <section class="form signup">
        <header>Chat Applicatie</header>
        <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
          <div class="error-text"></div>
          <div class="name-details">
            <div class="field input">
              <label>Voornaam</label>
              <input type="text" name="fname" placeholder="Voornaam" required>
            </div>
            <div class="field input">
              <label>Achternaam</label>
              <input type="text" name="lname" placeholder="Achternaam" required>
            </div>
          </div>
          <div class="field input">
            <label>Emailadres</label>
            <input type="text" name="email" placeholder="Voer uw Emailadres in" required>
          </div>
          <div class="field input">
            <label>Wachtwoord</label>
            <input type="password" name="password" placeholder="Voer een wachtwoord in" required>
            <i class="fas fa-eye"></i>
          </div>
          <div class="field image">
            <label>Selecteer een foto</label>
            <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
          </div>
          <div class="field button">
            <input type="submit" name="submit" value="Ga door naar Chat">
          </div>
        </form>
        <div class="link">Al een account? <a href="login.php">Login</a></div>
      </section>
    </div>
  
    <script src="js/pass-show-hide.js"></script>
    <script src="js/signup.js"></script>
  
  </body>
</html>
